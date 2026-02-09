<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStatusUpdateRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Resources\TaskResource;
use App\Models\Client;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(protected TaskService $taskService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Task::with(['user', 'client']);

        // Filtering
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->has('date_from')) {
            $query->whereDate('deadline', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('deadline', '<=', $request->date_to);
        }

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
            ->where('status', '!=', TaskStatus::Done)
            ->where('status', '!=', TaskStatus::Cancelled)
            ->where('deadline', '<', now())
            ->get();

        return TaskResource::collection($tasks);
    }

    public function store(TaskStoreRequest $request): TaskResource
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = TaskStatus::Pending;

        $task = $this->taskService->create($data);

        return new TaskResource($task->load(['user', 'client']));
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task->load(['user', 'client']));
    }

    use App\Http\Requests\TaskUpdateRequest;

    public function update(TaskUpdateRequest $request, Task $task): TaskResource
    {
        $task = $this->taskService->update($task, $request->validated());

        return new TaskResource($task->load(['user', 'client']));
    }

    public function updateStatus(TaskStatusUpdateRequest $request, Task $task): TaskResource
    {
        $newStatus = TaskStatus::tryFrom($request->status);

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
        $tasks = $client->tasks()->with(['user'])->paginate(15);

        return TaskResource::collection($tasks);
    }
}
