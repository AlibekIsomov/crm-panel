<?php

namespace App\Enums;

enum UserRole: string
{
    case Manager = 'manager';
    case Admin = 'admin';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Manager => 'Manager',
            self::Admin => 'Administrator',
        };
    }
}
