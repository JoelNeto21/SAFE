<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuthorizationType;

class AuthorizationTypeSeeder extends Seeder
{
    public function run(): void
    {
        AuthorizationType::create([
            'name' => 'Entrada'
        ]);

        AuthorizationType::create([
            'name' => 'Saída'
        ]);
    }
}
