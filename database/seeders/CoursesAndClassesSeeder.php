<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CoursesAndClassesSeeder extends Seeder
{
    public function run(): void
    {
        $courses = collect([
            [
                'name' => 'Desenvolvimento de Sistemas',
                'slug' => 'desenvolvimento-de-sistemas',
                'prefix' => 'DS',
                'classrooms' => ['DS-1A', 'DS-2A', 'DS-3A'],
            ],
            [
                'name' => 'Eletroeletrônica',
                'slug' => 'eletroeletronica',
                'prefix' => 'ELT',
                'classrooms' => ['ELT-1A', 'ELT-2A', 'ELT-3A'],
            ],
        ])->mapWithKeys(function (array $data): array {
            $course = Course::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => "Curso técnico de {$data['name']} usado nos fluxos de teste do SAFE.",
                ],
            );

            return [$data['slug'] => array_merge($data, ['model' => $course])];
        });

        $bruno = User::where('email', 'bruno@safe.com')->first();
        $samuel = User::where('email', 'samuel@safe.com')->first();
        $eduardo = User::where('email', 'eduardo@safe.com')->first();

        $students = [
            'DS-1A' => ['Ana Beatriz Martins', 'Lucas Ferreira Lima', 'Mariana Souza Rocha', 'Pedro Henrique Alves', 'Giovana Ribeiro Costa'],
            'DS-2A' => ['Rafael Gomes Nunes', 'Isabela Moreira Santos', 'Caio Vinícius Pereira', 'Lara Cristina Melo', 'Nicolas Carvalho Duarte'],
            'DS-3A' => ['Amanda Vitória Castro', 'Enzo Gabriel Oliveira', 'Julia Fernandes Araújo', 'Miguel Augusto Cardoso', 'Sophia Almeida Teixeira'],
            'ELT-1A' => ['Matheus Henrique Ramos', 'Laura Beatriz Cunha', 'Davi Luiz Barbosa', 'Clara Valentina Reis', 'Arthur Felipe Mendes'],
            'ELT-2A' => ['Helena Sofia Batista', 'João Pedro Farias', 'Manuela Correia Dias', 'Bernardo Silva Pacheco', 'Valentina Moura Freitas'],
            'ELT-3A' => ['Luiza Helena Azevedo', 'Gustavo Martins Vieira', 'Maria Eduarda Peixoto', 'Theo Augusto Monteiro', 'Yasmin Carvalho Assis'],
        ];

        foreach ($courses as $courseData) {
            foreach ($courseData['classrooms'] as $index => $classroomName) {
                $mainTeacher = $courseData['prefix'] === 'DS' ? $bruno : $samuel;

                $classroom = Classroom::updateOrCreate(
                    ['name' => $classroomName],
                    [
                        'course_id' => $courseData['model']->id,
                        'course' => $courseData['model']->name,
                        'teacher_id' => $mainTeacher?->id,
                    ],
                );

                $classroom->teachers()->sync(collect([$mainTeacher?->id, $eduardo?->id])->filter()->all());

                foreach ($students[$classroomName] as $position => $studentName) {
                    Student::updateOrCreate(
                        ['registration' => $this->registration($courseData['prefix'], $index, $position)],
                        [
                            'name' => $studentName,
                            'classroom_id' => $classroom->id,
                        ],
                    );
                }
            }
        }
    }

    protected function registration(string $prefix, int $classroomIndex, int $studentIndex): string
    {
        return Str::of($prefix)
            ->append('2026')
            ->append((string) ($classroomIndex + 1))
            ->append(str_pad((string) ($studentIndex + 1), 2, '0', STR_PAD_LEFT))
            ->toString();
    }
}
