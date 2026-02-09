<?php

namespace App\Console\Commands;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Jobs\SendTaskReminderJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTaskReminders extends Command
{
    protected $signature = 'tasks:check-reminders';
    protected $description = 'Check for tasks needing reminders and overdue tasks';

    public function handle()
    {
        $candidates = Task::where(function ($query) {
            $query->where('status', TaskStatus::Pending)
                ->orWhere('status', TaskStatus::InProgress);
        })
            ->whereNull('reminder_sent_at')
            ->whereNotNull('remind_before_minutes')
            ->get();

        foreach ($candidates as $task) {
            $reminderTime = $task->deadline->copy()->subMinutes($task->remind_before_minutes);

            if ($reminderTime->lte(now())) {
                SendTaskReminderJob::dispatch($task);
                $this->info("Dispatched reminder for Task #{$task->id}");
            }
        }

        // 2. Check overdue tasks
        $overdue = Task::where('status', '!=', TaskStatus::Done)
            ->where('status', '!=', TaskStatus::Cancelled)
            ->where('deadline', '<', now())
            ->get();

        foreach ($overdue as $task) {
            Log::info("Task #{$task->id} is OVERDUE: {$task->title} (Deadline: {$task->deadline})");
        }
    }
}
