<?php

namespace App\Jobs;

use App\Models\Task;
use App\Enums\ReminderChannel;
use App\Notifications\TaskReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTaskReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Task $task)
    {
    }

    public function handle(): void
    {
        if ($this->task->status->isFinal()) {
            return;
        }

        if ($this->task->reminder_sent_at) {
            return;
        }

        if ($this->task->remind_via === ReminderChannel::Email) {
            $this->task->user->notify(new TaskReminderNotification($this->task));
            Log::info("Email reminder sent for task {$this->task->id}");
        } elseif ($this->task->remind_via === ReminderChannel::Sms) {
            Log::channel('daily')->info("[SMS MOCK] To: {$this->task->client?->phone} | {$this->task->user->phone} - Task: {$this->task->title} due at {$this->task->deadline}");
        }

        $this->task->update(['reminder_sent_at' => now()]);
    }
}
