<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use UnitEnum;

abstract class BaseResource extends Resource
{
    protected static string | UnitEnum | null $navigationGroup = null;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
