<?php

namespace App\Enums;

use App\Contracts\HasLabel;

enum Status: string implements HasLabel
{
    case Active = 'active';

    public function label(): string
    {
        return Translator::get($this->value);
    }
}
