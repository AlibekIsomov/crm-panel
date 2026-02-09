<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function changeStatus(Task $task, TaskStatus $newStatus): Task
    {
        if ($task->status === $newStatus) {
            return $task;
        }

        if (!$task->status->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => ["Transition from {$task->status->value} to {$newStatus->value} is not allowed"],
            ]);
        }

        return DB::transaction(function () use ($task, $newStatus) {
            $task->update([
                'status' => $newStatus,
                'completed_at' => $newStatus === TaskStatus::Done ? now() : null,
            ]);

            if ($task->is_recurring && $newStatus === TaskStatus::Done) {
                $this->createNextRecurringTask($task);
            }

            return $task;
        });
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    protected function createNextRecurringTask(Task $originalTask): void
    {
        if (!$originalTask->recurrence_type) {
            return;
        }

        $daysToAdd = $originalTask->recurrence_type->getDaysToAdd();
        $newDeadline = $originalTask->deadline->copy()->addDays($daysToAdd);

        $newTaskData = $originalTask->replicate([
            'status',
            'reminder_sent_at',
            'completed_at',
            'created_at',
            'updated_at',
            'id'
        ])->toArray();

        $newTaskData['status'] = TaskStatus::Pending;
        $newTaskData['deadline'] = $newDeadline;

        $newTaskData['user_id'] = $originalTask->user_id;
        $newTaskData['client_id'] = $originalTask->client_id;
        $newTaskData['is_recurring'] = true;

        Task::create($newTaskData);
    }
}
