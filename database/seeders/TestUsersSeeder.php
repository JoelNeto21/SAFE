<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Classroom;
use App\Models\Student;

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

        // Criar uma turma atribuída ao professor e alguns alunos de exemplo
        $classroom = Classroom::firstOrCreate([
            'name' => 'Turma A',
        ], [
            'course' => 'Curso Exemplo',
            'teacher_id' => $professor->id,
        ]);

        Student::firstOrCreate([
            'registration' => '2026001',
        ], [
            'name' => 'Aluno Um',
            'classroom_id' => $classroom->id,
        ]);

        Student::firstOrCreate([
            'registration' => '2026002',
        ], [
            'name' => 'Aluno Dois',
            'classroom_id' => $classroom->id,
        ]);

        $portaria = User::firstOrCreate(
            ['email' => 'portaria@safe.com'],
            ['name' => 'Portaria', 'password' => bcrypt('12345678')]
        );
        $portaria->assignRole('portaria');
    }
}
