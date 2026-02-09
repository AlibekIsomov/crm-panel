<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification
{
    use Queueable;

    public function __construct(protected Task $task) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: '.$this->task->title)
            ->line('This is a reminder for your task due at '.$this->task->deadline->format('Y-m-d H:i'))
            ->line('Title: '.$this->task->title)
            ->line('Priority: '.$this->task->priority->value)
            ->action('View Task', url('/tasks/'.$this->task->id));
    }
}
