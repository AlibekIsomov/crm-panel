<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStatusUpdateRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Client;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    private const FILTER_FIELDS = ['type', 'priority', 'status', 'client_id'];

    private const DATE_FILTERS = ['date_from' => '>=', 'date_to' => '<='];

    public function __construct(protected TaskService $taskService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Task::with(['user', 'client']);

        $this->applyFilters($query, $request);
        $this->applyDateFilters($query, $request);

        return TaskResource::collection($query->paginate(15));
    }

    public function today(): AnonymousResourceCollection
    {
        $tasks = Task::with(['user', 'client'])
            ->whereDate('deadline', now())
            ->get();

        return TaskResource::collection($tasks);
    }

    public function overdue(): AnonymousResourceCollection
    {
        $tasks = Task::with(['user', 'client'])
            ->whereNotIn('status', [TaskStatus::Done, TaskStatus::Cancelled])
            ->where('deadline', '<', now())
            ->get();

        return TaskResource::collection($tasks);
    }

    public function store(TaskStoreRequest $request): TaskResource
    {
        $data = array_merge($request->validated(), [
            'user_id' => $request->user()->id,
            'status' => TaskStatus::Pending,
        ]);

        $task = $this->taskService->create($data);

        return new TaskResource($task->load(['user', 'client']));
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task->load(['user', 'client']));
    }

    public function update(TaskUpdateRequest $request, Task $task): TaskResource
    {
        $task = $this->taskService->update($task, $request->validated());

        return new TaskResource($task->load(['user', 'client']));
    }

    public function updateStatus(TaskStatusUpdateRequest $request, Task $task): TaskResource
    {
        $newStatus = TaskStatus::from($request->status);
        $task = $this->taskService->changeStatus($task, $newStatus);

        return new TaskResource($task->load(['user', 'client']));
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task);

        return response()->json(null, 204);
    }

    public function getClientTasks(Client $client): AnonymousResourceCollection
    {
        return TaskResource::collection(
            $client->tasks()->with(['user'])->paginate(15)
        );
    }

    private function applyFilters($query, Request $request): void
    {
        foreach (self::FILTER_FIELDS as $field) {
            if ($request->has($field)) {
                $query->where($field, $request->input($field));
            }
        }
    }

    private function applyDateFilters($query, Request $request): void
    {
        foreach (self::DATE_FILTERS as $param => $operator) {
            if ($request->has($param)) {
                $query->whereDate('deadline', $operator, $request->input($param));
            }
        }
    }
}
