<?php

namespace App\Enums;

enum OccurrenceStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Aberta',
            self::InProgress => 'Em acompanhamento',
            self::Closed => 'Encerrada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'warning',
            self::InProgress => 'info',
            self::Closed => 'success',
        };
    }
}
