<?php

namespace App\Enums;

enum Subscription: string
{
    case free = 'free';
    case basic = 'basic';
    case advanced = 'advanced';
    case etika = 'etika';
    case C5 = 'C5';
    case C10 = 'C10';
    case CIlimited = 'CIlimited';

    public function label(): string
    {
        return match ($this) {
            self::free => 'GRÁTIS',
            self::basic => 'BÁSICO',
            self::advanced => 'AVANÇADO',
            self::etika => 'CONTABILIDADE PARA IGREJAS',
            self::C5 => 'C5 - 5 LICENÇAS',
            self::C10 => 'C10 - 10 LICENÇAS',
            self::CIlimited => 'CI - 20 LICENÇAS',
        };
    }
}
