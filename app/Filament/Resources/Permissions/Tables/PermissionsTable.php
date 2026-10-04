<?php

namespace App\Filament\Resources\Permissions\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Permission'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->label(__('Guard'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->date()
                    ->badge()
                    ->color('primary'),
            ]);
    }
}
