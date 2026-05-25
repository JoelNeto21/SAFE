<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'aqv', 'professor', 'portaria'] as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $users = [
            ['email' => 'admin@safe.com', 'name' => 'Administrador SAFE', 'role' => 'admin'],
            ['email' => 'aqv@safe.com', 'name' => 'Equipe AQV', 'role' => 'aqv'],
            ['email' => 'portaria@safe.com', 'name' => 'Equipe Portaria', 'role' => 'portaria'],
            ['email' => 'eduardo@safe.com', 'name' => 'Professor Eduardo', 'role' => 'professor'],
            ['email' => 'samuel@safe.com', 'name' => 'Professor Samuel', 'role' => 'professor'],
            ['email' => 'bruno@safe.com', 'name' => 'Professor Bruno', 'role' => 'professor'],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => '12345678',
                    'sector' => $data['role'],
                    'is_active' => true,
                ],
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
