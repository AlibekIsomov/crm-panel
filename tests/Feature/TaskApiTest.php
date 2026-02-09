<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_can_create_task_with_reminder(): void
    {
        $user = $this->authenticate();
        $client = Client::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'title' => 'Call Client',
            'type' => 'call',
            'priority' => 'high',
            'client_id' => $client->id,
            'deadline' => now()->addDay()->toDateTimeString(),
            'remind_before_minutes' => 30,
            'remind_via' => 'email',
            'is_recurring' => false,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Call Client')
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Call Client',
            'user_id' => $user->id,
            'remind_before_minutes' => 30,
        ]);
    }

    public function test_can_change_status_valid_transition(): void
    {
        $this->authenticate();
        $task = Task::factory()->create(['status' => TaskStatus::Pending]);

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'in_progress');
    }

    public function test_cannot_change_status_invalid_transition(): void
    {
        $this->authenticate();
        $task = Task::factory()->create(['status' => TaskStatus::Pending]);

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'done',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_can_get_today_tasks(): void
    {
        $user = $this->authenticate();

        // Task for today
        Task::factory()->for($user)->create([
            'deadline' => now()->startOfHour(),
            'title' => 'Today Task'
        ]);

        Task::factory()->for($user)->create([
            'deadline' => now()->addDay(),
            'title' => 'Tomorrow Task'
        ]);

        $response = $this->getJson('/api/tasks/today');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Today Task');
    }

    public function test_can_get_overdue_tasks(): void
    {
        $user = $this->authenticate();

        Task::factory()->for($user)->create([
            'deadline' => now()->subHour(),
            'status' => TaskStatus::Pending,
            'title' => 'Late Task'
        ]);

        Task::factory()->for($user)->create([
            'deadline' => now()->addHour(),
            'status' => TaskStatus::Pending
        ]);

        $response = $this->getJson('/api/tasks/overdue');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Late Task');
    }
}
