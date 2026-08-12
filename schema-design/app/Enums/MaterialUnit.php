<?php

namespace App\Enums;

enum MaterialUnit: string
{
    case Sheet      = 'sheet';
    case Milliliter = 'ml';
    case Gram       = 'gram';
    case Piece      = 'piece';
    case Roll       = 'roll';

    public function label(): string
    {
        return match ($this) {
            self::Sheet      => 'ورقة',
            self::Milliliter => 'مليلتر',
            self::Gram       => 'جرام',
            self::Piece      => 'قطعة',
            self::Roll       => 'لفة',
        };
    }
}
