<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use UnitEnum;

abstract class BaseResource extends Resource
{
    protected static string | UnitEnum | null $navigationGroup = null;

    public static function getNavigationGroup(): ?string
    {
        return __('School Management');
    }
}
