<?php

namespace Database\Factories;

use App\Enums\RecurrenceType;
use App\Enums\ReminderChannel;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'type' => fake()->randomElement(TaskType::values()),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'priority' => fake()->randomElement(TaskPriority::values()),
            'status' => TaskStatus::Pending,
            'deadline' => fake()->dateTimeBetween('now', '+1 month'),
            'is_recurring' => false,
            'recurrence_type' => null,
            'remind_before_minutes' => fake()->optional()->randomElement([15, 30, 60, 120]),
            'remind_via' => fake()->optional()->randomElement(ReminderChannel::values()),
        ];
    }

    public function recurring(RecurrenceType $type = RecurrenceType::Weekly): static
    {
        return $this->state(fn(array $attributes) => [
            'is_recurring' => true,
            'recurrence_type' => $type,
        ]);
    }

    public function withReminder(int $minutes = 30, ReminderChannel $channel = ReminderChannel::Email): static
    {
        return $this->state(fn(array $attributes) => [
            'remind_before_minutes' => $minutes,
            'remind_via' => $channel,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn(array $attributes) => [
            'deadline' => fake()->dateTimeBetween('-1 week', '-1 hour'),
            'status' => TaskStatus::Pending,
        ]);
    }

    public function forToday(): static
    {
        return $this->state(fn(array $attributes) => [
            'deadline' => now()->endOfDay(),
        ]);
    }
}
