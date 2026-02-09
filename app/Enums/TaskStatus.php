<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function canTransitionTo(TaskStatus $newStatus): bool
    {
        return match ($this) {
            self::Pending => in_array($newStatus, [self::InProgress, self::Cancelled]),
            self::InProgress => in_array($newStatus, [self::Done, self::Cancelled]),
            self::Done, self::Cancelled => false,
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Done, self::Cancelled]);
    }
}
