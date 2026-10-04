<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DiscountType: string implements HasLabel
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Percentage => __('Percentage'),
            self::Fixed => __('Fixed'),
        };
    }
}
