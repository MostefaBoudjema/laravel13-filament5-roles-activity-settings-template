<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case Cash = 'cash';
    case Check = 'check';
    case BankTransfer = 'bank_transfer';
    case Ccp = 'ccp';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Cash => __('Cash'),
            self::Check => __('Check'),
            self::BankTransfer => __('Bank Transfer'),
            self::Ccp => __('CCP'),
        };
    }
}
