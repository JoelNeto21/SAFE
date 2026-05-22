<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Criar os cargos
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'aqv', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'professor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'portaria', 'guard_name' => 'web']);

        // Criar ou encontrar usuários
        $admin = User::firstOrCreate(
            ['email' => 'admin@safe.com'],
            ['name' => 'Administrador', 'password' => bcrypt('12345678')]
        );
        $admin->assignRole('admin');

        $aqv = User::firstOrCreate(
            ['email' => 'aqv@safe.com'],
            ['name' => 'AQV', 'password' => bcrypt('12345678')]
        );
        $aqv->assignRole('aqv');

        $professor = User::firstOrCreate(
            ['email' => 'professor@safe.com'],
            ['name' => 'Professor', 'password' => bcrypt('12345678')]
        );
        $professor->assignRole('professor');

        $portaria = User::firstOrCreate(
            ['email' => 'portaria@safe.com'],
            ['name' => 'Portaria', 'password' => bcrypt('12345678')]
        );
        $portaria->assignRole('portaria');
    }
}
