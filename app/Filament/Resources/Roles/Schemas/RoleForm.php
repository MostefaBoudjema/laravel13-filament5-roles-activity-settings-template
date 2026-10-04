<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Filament\Forms\Components\PermissionMatrix;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Role Name'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                PermissionMatrix::make('permissions')
                    ->label(__('Permissions'))
                    ->columnSpanFull(),
            ]);
    }
}
