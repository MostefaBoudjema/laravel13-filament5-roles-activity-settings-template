<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Pages\BaseCreateRecord;
use App\Filament\Resources\Permissions\PermissionResource;

class CreatePermission extends BaseCreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected function afterCreate(): void
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this->record)
            ->useLog('permissions')
            ->log("Permission '{$this->record->name}' was created.");
    }
}
