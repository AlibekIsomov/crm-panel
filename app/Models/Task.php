<?php

namespace App\Models;

use App\Enums\RecurrenceType;
use App\Enums\ReminderChannel;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'type',
        'title',
        'description',
        'priority',
        'status',
        'deadline',
        'is_recurring',
        'recurrence_type',
        'remind_before_minutes',
        'remind_via',
        'reminder_sent_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => TaskType::class,
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'recurrence_type' => RecurrenceType::class,
            'remind_via' => ReminderChannel::class,
            'deadline' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_recurring' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function isOverdue(): bool
    {
        return !$this->status->isFinal() && $this->deadline->isPast();
    }

    public function needsReminder(): bool
    {
        if ($this->reminder_sent_at || !$this->remind_before_minutes || $this->status->isFinal()) {
            return false;
        }

        $reminderTime = $this->deadline->subMinutes($this->remind_before_minutes);
        return now()->gte($reminderTime);
    }
}
