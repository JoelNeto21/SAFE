<?php

namespace Database\Seeders;

use App\Models\AuthorizationType;
use Illuminate\Database\Seeder;

class AuthorizationTypeSeeder extends Seeder
{
    public function run(): void
    {
        AuthorizationType::query()
            ->where('name', 'SaÃƒÂ­da')
            ->update(['name' => 'Saída']);

        foreach (['Entrada', 'Saída'] as $type) {
            AuthorizationType::updateOrCreate(['name' => $type]);
        }
    }
}
