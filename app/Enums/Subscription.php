<?php

namespace App\Enums;

enum Subscription: string
{
    case free = 'free';
    case basic = 'basic';
    case advanced = 'advanced';

    public function label(): string
    {
        return match ($this) {
            self::free => 'GRÁTIS',
            self::basic => 'BÁSICA',
            self::advanced => 'AVANÇADA',
        };
    }
}
