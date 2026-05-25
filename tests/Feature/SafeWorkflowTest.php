<?php

namespace Tests\Feature;

use App\Enums\AuthorizationStatus;
use App\Enums\OccurrenceStatus;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Authorization;
use App\Models\AuthorizationAudit;
use App\Models\AuthorizationType;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\InternalMessage;
use App\Models\Occurrence;
use App\Models\OccurrenceAudit;
use App\Models\Student;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafeWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_create_courses_classrooms_students_and_teacher_scopes(): void
    {
        $this->seed();

        $this->assertSame(2, Course::count());
        $this->assertSame(6, Classroom::count());
        $this->assertSame(30, Student::count());

        $eduardo = User::where('email', 'eduardo@safe.com')->firstOrFail();
        $samuel = User::where('email', 'samuel@safe.com')->firstOrFail();
        $bruno = User::where('email', 'bruno@safe.com')->firstOrFail();

        $this->assertTrue($eduardo->hasRole('professor'));
        $this->assertSame(6, $eduardo->teachingClassrooms()->count());
        $this->assertSame(3, $samuel->teachingClassrooms()->count());
        $this->assertSame(3, $bruno->teachingClassrooms()->count());

        $this->actingAs($eduardo);
        $this->assertSame(30, StudentResource::getEloquentQuery()->count());

        $this->actingAs($samuel);
        $this->assertSame(15, StudentResource::getEloquentQuery()->count());
        $this->assertSame(
            ['Eletroeletrônica'],
            StudentResource::getEloquentQuery()
                ->with('classroom')
                ->get()
                ->pluck('classroom.course')
                ->unique()
                ->values()
                ->all(),
        );

        $this->actingAs($bruno);
        $this->assertSame(15, StudentResource::getEloquentQuery()->count());
        $this->assertSame(
            ['Desenvolvimento de Sistemas'],
            StudentResource::getEloquentQuery()
                ->with('classroom')
                ->get()
                ->pluck('classroom.course')
                ->unique()
                ->values()
                ->all(),
        );
    }

    public function test_exit_authorization_flow_notifies_teacher_and_gate(): void
    {
        $this->seed();

        $aqv = User::where('email', 'aqv@safe.com')->firstOrFail();
        $bruno = User::where('email', 'bruno@safe.com')->firstOrFail();
        $portaria = User::where('email', 'portaria@safe.com')->firstOrFail();
        $student = Student::whereHas('classroom', fn ($query) => $query->where('course', 'Desenvolvimento de Sistemas'))->firstOrFail();
        $exitType = AuthorizationType::where('name', 'Saída')->firstOrFail();

        $authorization = Authorization::create([
            'student_id' => $student->id,
            'authorization_type_id' => $exitType->id,
            'requested_by' => $aqv->id,
            'event_at' => now(),
            'responsible_name' => 'Responsável pedagógico',
            'reason' => 'Consulta médica agendada.',
            'observations' => 'Documento conferido pela AQV.',
        ]);

        $this->assertSame(AuthorizationStatus::Pending, $authorization->fresh()->status);
        $this->assertDatabaseHas('authorization_audits', [
            'authorization_id' => $authorization->id,
            'action' => 'created',
        ]);
        $this->assertSame(1, $bruno->notifications()->whereNull('read_at')->count());

        $authorization->markAsRead($bruno, 'Lido em sala.');
        $authorization->approve($bruno, 'Aluno liberado pelo professor.');

        $authorization->refresh();
        $this->assertSame(AuthorizationStatus::Approved, $authorization->status);
        $this->assertNotNull($authorization->read_at);
        $this->assertNotNull($authorization->authorized_at);
        $this->assertSame($bruno->id, $authorization->processed_by);
        $this->assertSame(1, $portaria->notifications()->whereNull('read_at')->count());

        $authorization->finish($portaria, 'Saída confirmada na portaria.');

        $authorization->refresh();
        $this->assertSame(AuthorizationStatus::Finished, $authorization->status);
        $this->assertNotNull($authorization->completed_at);
        $this->assertSame(4, AuthorizationAudit::where('authorization_id', $authorization->id)->count());
    }

    public function test_occurrences_and_internal_messages_generate_history_and_notifications(): void
    {
        $this->seed();

        $aqv = User::where('email', 'aqv@safe.com')->firstOrFail();
        $eduardo = User::where('email', 'eduardo@safe.com')->firstOrFail();
        $student = Student::firstOrFail();

        $occurrence = Occurrence::create([
            'student_id' => $student->id,
            'registered_by' => $aqv->id,
            'occurred_at' => now(),
            'description' => 'Aluno precisou de acompanhamento após atraso recorrente.',
            'status' => OccurrenceStatus::Open,
        ]);

        $this->assertDatabaseHas('occurrence_audits', [
            'occurrence_id' => $occurrence->id,
            'action' => 'created',
        ]);
        $this->assertGreaterThan(0, $eduardo->notifications()->count());

        $occurrence->close($aqv, 'Responsável orientado.');
        $this->assertSame(OccurrenceStatus::Closed, $occurrence->fresh()->status);
        $this->assertSame(2, OccurrenceAudit::where('occurrence_id', $occurrence->id)->count());

        $message = InternalMessage::create([
            'sender_id' => $aqv->id,
            'recipient_id' => $eduardo->id,
            'subject' => 'Acompanhamento de autorização',
            'body' => 'Favor validar o fluxo no início da próxima aula.',
        ]);

        $this->assertSame(1, SystemNotification::query()
            ->where('notifiable_id', $eduardo->id)
            ->where('data', 'like', '%Nova mensagem interna%')
            ->count());

        $message->markAsRead();
        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_main_admin_pages_render_for_admin_user(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@safe.com')->firstOrFail();

        foreach ([
            '/admin',
            '/admin/employees',
            '/admin/courses',
            '/admin/classrooms',
            '/admin/students',
            '/admin/authorizations',
            '/admin/occurrences',
            '/admin/internal-messages',
            '/admin/notifications',
            '/admin/authorization-audits',
            '/admin/occurrence-audits',
        ] as $uri) {
            $response = $this->actingAs($admin)->get($uri);

            $this->assertTrue(
                $response->isSuccessful(),
                "{$uri} returned {$response->getStatusCode()}",
            );
        }
    }

    public function test_professor_cannot_access_employee_management(): void
    {
        $this->seed();

        $professor = User::where('email', 'bruno@safe.com')->firstOrFail();

        $this->actingAs($professor)
            ->get('/admin/employees')
            ->assertForbidden();
    }
}
