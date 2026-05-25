<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            AuthorizationTypeSeeder::class,
            TestUsersSeeder::class,
            CoursesAndClassesSeeder::class,
        ]);

        $testUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'sector' => 'admin',
                'is_active' => true,
            ],
        );

        $testUser->syncRoles(['admin']);
    }
}
