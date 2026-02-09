<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = 'password';

    private const CLIENT_COUNT = 10;

    public function run(): void
    {
        $manager = $this->createManager();
        $this->createAdmin();
        $clients = $this->createClients();

        $this->createTasksForUser($manager, $clients);
    }

    private function createManager(): User
    {
        return User::factory()->manager()->create([
            'name' => 'Manager User',
            'email' => 'manager@crm.com',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
        ]);
    }

    private function createAdmin(): User
    {
        return User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@crm.com',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
        ]);
    }

    private function createClients()
    {
        return Client::factory(self::CLIENT_COUNT)->create();
    }

    private function createTasksForUser(User $user, $clients): void
    {
        $taskConfigs = [
            ['count' => 5, 'state' => null],
            ['count' => 3, 'state' => 'recurring'],
            ['count' => 2, 'state' => 'overdue'],
            ['count' => 2, 'state' => 'forToday'],
            ['count' => 2, 'state' => 'withReminder'],
        ];

        foreach ($taskConfigs as $config) {
            $factory = Task::factory($config['count'])->for($user);

            if ($config['state']) {
                $factory = $factory->{$config['state']}();
            }

            $factory->for($clients->random())->create();
        }
    }
}
