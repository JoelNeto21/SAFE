<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'aqv']);
        Role::create(['name' => 'professor']);
        Role::create(['name' => 'portaria']);
    }
}
