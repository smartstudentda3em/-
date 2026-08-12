<?php

namespace App\Enums;

enum PaperSize: string
{
    case A3     = 'A3';
    case A4     = 'A4';
    case A5     = 'A5';
    case Letter = 'letter';

    public function label(): string
    {
        return match ($this) {
            self::A3     => 'A3',
            self::A4     => 'A4',
            self::A5     => 'A5',
            self::Letter => 'Letter',
        };
    }
}
