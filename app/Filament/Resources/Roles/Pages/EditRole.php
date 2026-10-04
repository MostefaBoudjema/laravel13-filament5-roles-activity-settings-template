<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * Pre-fill the form: convert the role's permissions to an array of names.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = $this->record->permissions->pluck('name')->toArray();
        return $data;
    }

    /**
     * Before saving: sync permissions from the array of names.
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $permissions = $data['permissions'] ?? [];

        // permissions may be JSON-encoded by the hidden input
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?? [];
        }

        unset($data['permissions']);

        $record->fill($data)->save();
        $record->syncPermissions($permissions);

        return $record;
    }

    protected function afterSave(): void
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this->record)
            ->useLog('roles')
            ->log("Role '{$this->record->name}' was updated.");
    }
}
