<?php

namespace App\Enums;

enum BindingType: string
{
    case None      = 'none';
    case Staple    = 'staple';
    case Spiral    = 'spiral';
    case Glue      = 'glue';
    case Hardcover = 'hardcover';

    public function label(): string
    {
        return match ($this) {
            self::None      => 'بدون تجليد',
            self::Staple    => 'دبوس',
            self::Spiral    => 'سلك حلزوني',
            self::Glue      => 'لاصق حراري',
            self::Hardcover => 'غلاف مقوّى',
        };
    }
}
