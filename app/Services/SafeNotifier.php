<?php

namespace App\Services;

use App\Models\Authorization;
use App\Models\InternalMessage;
use App\Models\User;
use App\Notifications\SafeDatabaseNotification;
use Illuminate\Support\Collection;

class SafeNotifier
{
    public function notifyUsers(iterable $users, string $title, string $body, ?string $url = null, string $category = 'info', string $color = 'primary'): void
    {
        collect($users)
            ->filter(fn ($user) => $user instanceof User && $user->is_active)
            ->unique('id')
            ->each(fn (User $user) => $user->notify(
                new SafeDatabaseNotification($title, $body, $url, $category, $color)
            ));
    }

    public function notifyRole(string $role, string $title, string $body, ?string $url = null, string $category = 'info', string $color = 'primary'): void
    {
        $this->notifyUsers(
            User::role($role)->where('is_active', true)->get(),
            $title,
            $body,
            $url,
            $category,
            $color,
        );
    }

    public function notifyAuthorizationCreated(Authorization $authorization): void
    {
        $authorization->loadMissing('student', 'teacher', 'type');

        $this->notifyUsers(
            $this->teachersFor($authorization),
            'Nova autorização digital',
            sprintf(
                '%s precisa de validação para %s.',
                $authorization->student?->name ?? 'Aluno',
                $authorization->type?->name ?? 'autorização'
            ),
            $this->authorizationUrl($authorization),
            'authorization',
            'warning',
        );

        if ($authorization->isExitFlow()) {
            $this->notifyUsers(
                User::role('portaria')->where('is_active', true)->get(),
                'Autorização de saída registrada',
                sprintf(
                    'Há uma saída antecipada para %s em acompanhamento.',
                    $authorization->student?->name ?? 'aluno'
                ),
                $this->authorizationUrl($authorization),
                'authorization',
                'info',
            );
        }
    }

    public function notifyAuthorizationRead(Authorization $authorization, User $reader): void
    {
        if ($authorization->requester) {
            $this->notifyUsers(
                [$authorization->requester],
                'Autorização lida',
                "{$reader->name} confirmou leitura da autorização #{$authorization->id}.",
                $this->authorizationUrl($authorization),
                'authorization',
                'info',
            );
        }
    }

    public function notifyAuthorizationApproved(Authorization $authorization, User $approver): void
    {
        $authorization->loadMissing('student', 'type', 'requester');

        $targets = collect([$authorization->requester]);
        if ($authorization->isExitFlow()) {
            $targets = $targets->merge(User::role('portaria')->where('is_active', true)->get());
        }

        $this->notifyUsers(
            $targets,
            $authorization->isExitFlow() ? 'Saída liberada pelo professor' : 'Entrada aprovada e finalizada',
            "{$approver->name} aprovou a autorização de {$authorization->student?->name}.",
            $this->authorizationUrl($authorization),
            'authorization',
            'success',
        );
    }

    public function notifyAuthorizationDenied(Authorization $authorization, User $denier): void
    {
        $this->notifyUsers(
            collect([$authorization->requester])->merge($this->teachersFor($authorization)),
            'Autorização recusada',
            "{$denier->name} recusou a autorização #{$authorization->id}.",
            $this->authorizationUrl($authorization),
            'authorization',
            'danger',
        );
    }

    public function notifyAuthorizationFinished(Authorization $authorization, ?User $actor = null): void
    {
        $authorization->loadMissing('student');

        $this->notifyUsers(
            collect([$authorization->requester])->merge($this->teachersFor($authorization)),
            'Fluxo finalizado',
            sprintf(
                'A autorização de %s foi finalizada%s.',
                $authorization->student?->name ?? 'aluno',
                $actor ? " por {$actor->name}" : ''
            ),
            $this->authorizationUrl($authorization),
            'authorization',
            'success',
        );
    }

    public function notifyInternalMessage(InternalMessage $message): void
    {
        $message->loadMissing('sender', 'recipient');

        $targets = collect();
        if ($message->recipient) {
            $targets->push($message->recipient);
        }

        if ($message->recipient_role) {
            $targets = $targets->merge(User::role($message->recipient_role)->where('is_active', true)->get());
        }

        $this->notifyUsers(
            $targets,
            'Nova mensagem interna',
            sprintf('%s enviou: %s', $message->sender?->name ?? 'SAFE', $message->subject),
            '/admin/internal-messages',
            'message',
            'info',
        );
    }

    public function teachersFor(Authorization $authorization): Collection
    {
        $authorization->loadMissing('teacher', 'student.classroom.teachers', 'student.classroom.teacher');

        if ($authorization->teacher) {
            return collect([$authorization->teacher]);
        }

        $classroom = $authorization->student?->classroom;
        if (! $classroom) {
            return collect();
        }

        $teachers = $classroom->teachers;
        if ($classroom->teacher) {
            $teachers->push($classroom->teacher);
        }

        return $teachers;
    }

    protected function authorizationUrl(Authorization $authorization): string
    {
        return '/admin/authorizations';
    }
}
