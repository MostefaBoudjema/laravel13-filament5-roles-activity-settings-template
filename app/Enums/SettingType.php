<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SettingType: string implements HasLabel
{
    case Text = 'text';
    case Number = 'number';
    case Boolean = 'boolean';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Text => __('Text'),
            self::Number => __('Number'),
            self::Boolean => __('Boolean'),
        };
    }
}
