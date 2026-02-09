<?php

namespace Tests\Feature;

use App\Enums\RecurrenceType;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TaskService::class);
    }

    public function test_completing_recurring_task_creates_next_iteration(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => TaskStatus::InProgress,
            'is_recurring' => true,
            'recurrence_type' => RecurrenceType::Daily,
            'deadline' => now()->startOfHour(),
        ]);

        $this->service->changeStatus($task, TaskStatus::Done);

        $this->assertEquals(TaskStatus::Done, $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => $task->title,
            'status' => TaskStatus::Pending->value,
            'is_recurring' => true,
            'deadline' => $task->deadline->addDay()->toDateTimeString(),
        ]);

        $this->assertCount(2, Task::all());
    }

    public function test_cancelled_recurring_task_does_not_create_copy(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'is_recurring' => true,
            'recurrence_type' => RecurrenceType::Daily,
        ]);

        $this->service->changeStatus($task, TaskStatus::Cancelled);

        $this->assertEquals(TaskStatus::Cancelled, $task->fresh()->status);
        $this->assertCount(1, Task::all());
    }

    public function test_weekly_recurrence_adds_seven_days(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'is_recurring' => true,
            'recurrence_type' => RecurrenceType::Weekly,
            'deadline' => now()->startOfHour(),
        ]);

        $this->service->changeStatus($task, TaskStatus::Done);

        $newTask = Task::latest('id')->first();
        $this->assertEquals(
            $task->deadline->addDays(7)->toDateTimeString(),
            $newTask->deadline->toDateTimeString()
        );
    }
}
