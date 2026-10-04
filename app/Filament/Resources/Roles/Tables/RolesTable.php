<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Role'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('permissions.name')
                    ->label(__('Permissions'))
                    ->badge()
                    ->separator(',')
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->date()
                    ->badge()
                    ->color('primary'),
            ])
            ->defaultSort('name')
            ->filters([
                // Add role-scoped filters here if needed.
            ]);
    }
}
