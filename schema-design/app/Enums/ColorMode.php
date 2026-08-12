<?php

namespace App\Enums;

enum ColorMode: string
{
    case Color      = 'color';
    case BlackWhite = 'bw';

    public function label(): string
    {
        return match ($this) {
            self::Color      => 'ملوّن',
            self::BlackWhite => 'أبيض وأسود',
        };
    }
}
