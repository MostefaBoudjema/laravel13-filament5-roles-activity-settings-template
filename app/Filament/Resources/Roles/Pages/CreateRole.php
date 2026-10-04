<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * After creating the role, sync the selected permissions.
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $permissions = $data['permissions'] ?? [];

        // permissions may be JSON-encoded by the hidden input
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?? [];
        }

        unset($data['permissions']);

        $record = static::getModel()::create($data);
        $record->syncPermissions($permissions);

        return $record;
    }

    protected function afterCreate(): void
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this->record)
            ->useLog('roles')
            ->log("Role '{$this->record->name}' was created.");
    }
}
