<?php

namespace App\Enums;

/**
 * Enum representing user roles in the CRM system.
 */
enum UserRole: string
{
    case Manager = 'manager';
    case Admin = 'admin';

    /**
     * Get all available role values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get a human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Manager => 'Manager',
            self::Admin => 'Administrator',
        };
    }
}
