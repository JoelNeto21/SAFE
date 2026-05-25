<?php

namespace Tests\Feature;

use App\Enums\AuthorizationStatus;
use App\Filament\Resources\Authorizations\AuthorizationResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Authorization;
use App\Models\AuthorizationAudit;
use App\Models\AuthorizationType;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\InternalMessage;
use App\Models\Student;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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
        $eduardo = User::where('email', 'eduardo@safe.com')->firstOrFail();
        $portaria = User::where('email', 'portaria@safe.com')->firstOrFail();
        $student = Student::whereHas('classroom', fn ($query) => $query->where('course', 'Desenvolvimento de Sistemas'))->firstOrFail();
        $exitType = AuthorizationType::where('name', 'Saída')->firstOrFail();

        $authorization = Authorization::create([
            'student_id' => $student->id,
            'authorization_type_id' => $exitType->id,
            'teacher_id' => $bruno->id,
            'requested_by' => $aqv->id,
            'event_at' => today()->setTime(10, 0),
            'missed_classes' => ['class_3', 'class_4'],
            'responsible_name' => 'Responsável pedagógico',
            'reason' => 'Consulta médica agendada.',
            'observations' => 'Documento conferido pela AQV.',
        ]);

        $this->assertSame(AuthorizationStatus::Pending, $authorization->fresh()->status);
        $this->assertSame(['class_3', 'class_4'], $authorization->fresh()->missed_classes);
        $this->assertTrue($authorization->fresh()->event_at->isSameDay(today()));
        $this->assertDatabaseHas('authorization_audits', [
            'authorization_id' => $authorization->id,
            'action' => 'created',
        ]);
        $this->assertSame(1, $bruno->notifications()->whereNull('read_at')->count());
        $this->assertSame(0, $eduardo->notifications()->whereNull('read_at')->count());
        $this->assertSame(1, $portaria->notifications()->whereNull('read_at')->count());

        $notification = $bruno->notifications()->firstOrFail();
        $this->actingAs($bruno)
            ->get(data_get($notification->data, 'url'))
            ->assertSuccessful();

        $this->actingAs($bruno);
        $this->assertSame(1, AuthorizationResource::getEloquentQuery()->count());

        $this->actingAs($eduardo);
        $this->assertSame(0, AuthorizationResource::getEloquentQuery()->count());

        $authorization->markAsRead($bruno, 'Lido em sala.');
        $authorization->approve($bruno, 'Aluno liberado pelo professor.');

        $authorization->refresh();
        $this->assertSame(AuthorizationStatus::Approved, $authorization->status);
        $this->assertNotNull($authorization->read_at);
        $this->assertNotNull($authorization->authorized_at);
        $this->assertSame($bruno->id, $authorization->processed_by);
        $this->assertSame(2, $portaria->notifications()->whereNull('read_at')->count());

        $authorization->finish($portaria, 'Saída confirmada na portaria.');

        $authorization->refresh();
        $this->assertSame(AuthorizationStatus::Finished, $authorization->status);
        $this->assertNotNull($authorization->completed_at);
        $this->assertSame(1, AuthorizationAudit::where('authorization_id', $authorization->id)->count());
        $this->assertDatabaseHas('authorization_audits', [
            'authorization_id' => $authorization->id,
            'action' => 'finished',
        ]);

        $this->actingAs($bruno);
        $this->assertSame(0, AuthorizationResource::getEloquentQuery()->whereKey($authorization->id)->count());
    }

    public function test_entry_authorization_finishes_when_teacher_approves(): void
    {
        $this->seed();

        $aqv = User::where('email', 'aqv@safe.com')->firstOrFail();
        $bruno = User::where('email', 'bruno@safe.com')->firstOrFail();
        $portaria = User::where('email', 'portaria@safe.com')->firstOrFail();
        $student = Student::whereHas('classroom', fn ($query) => $query->where('course', 'Desenvolvimento de Sistemas'))->firstOrFail();
        $entryType = AuthorizationType::where('name', 'Entrada')->firstOrFail();

        $authorization = Authorization::create([
            'student_id' => $student->id,
            'authorization_type_id' => $entryType->id,
            'teacher_id' => $bruno->id,
            'requested_by' => $aqv->id,
            'event_at' => today()->setTime(8, 0),
            'responsible_name' => 'Responsável pedagógico',
            'reason' => 'Entrada tardia justificada.',
        ]);

        $authorization->approve($bruno, 'Entrada autorizada pelo professor.');

        $authorization->refresh();
        $this->assertSame(AuthorizationStatus::Finished, $authorization->status);
        $this->assertNotNull($authorization->approved_at);
        $this->assertNotNull($authorization->completed_at);
        $this->assertSame($bruno->id, $authorization->processed_by);
        $this->assertSame(0, $portaria->notifications()->whereNull('read_at')->count());
        $this->assertSame(1, AuthorizationAudit::where('authorization_id', $authorization->id)->count());
        $this->assertDatabaseHas('authorization_audits', [
            'authorization_id' => $authorization->id,
            'action' => 'finished',
        ]);
    }

    public function test_gate_can_approve_exit_after_teacher_read_without_teacher_approval(): void
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
            'teacher_id' => $bruno->id,
            'requested_by' => $aqv->id,
            'event_at' => today()->setTime(10, 0),
            'responsible_name' => 'Responsável pedagógico',
            'reason' => 'Saída antecipada justificada.',
        ]);

        $authorization->markAsRead($bruno, 'Professor ciente da saída.');
        $authorization->approveAtGate($portaria, 'Saída aprovada pela portaria.');

        $authorization->refresh();
        $this->assertSame(AuthorizationStatus::Finished, $authorization->status);
        $this->assertSame($portaria->id, $authorization->processed_by);
        $this->assertNotNull($authorization->read_at);
        $this->assertNotNull($authorization->approved_at);
        $this->assertNotNull($authorization->completed_at);
        $this->assertSame(1, AuthorizationAudit::where('authorization_id', $authorization->id)->count());
        $this->assertDatabaseHas('authorization_audits', [
            'authorization_id' => $authorization->id,
            'action' => 'finished',
        ]);
    }

    public function test_authorization_time_is_limited_to_institutional_period(): void
    {
        $this->seed();

        $aqv = User::where('email', 'aqv@safe.com')->firstOrFail();
        $bruno = User::where('email', 'bruno@safe.com')->firstOrFail();
        $student = Student::whereHas('classroom', fn ($query) => $query->where('course', 'Desenvolvimento de Sistemas'))->firstOrFail();
        $entryType = AuthorizationType::where('name', 'Entrada')->firstOrFail();

        $this->expectException(ValidationException::class);

        Authorization::create([
            'student_id' => $student->id,
            'authorization_type_id' => $entryType->id,
            'teacher_id' => $bruno->id,
            'requested_by' => $aqv->id,
            'event_at' => today()->setTime(7, 29),
            'responsible_name' => 'Responsável pedagógico',
            'reason' => 'Entrada fora do período permitido.',
        ]);
    }

    public function test_internal_messages_generate_notifications(): void
    {
        $this->seed();

        $aqv = User::where('email', 'aqv@safe.com')->firstOrFail();
        $eduardo = User::where('email', 'eduardo@safe.com')->firstOrFail();

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
            '/admin/authorizations/create',
            '/admin/internal-messages',
            '/admin/notifications',
            '/admin/authorization-audits',
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
