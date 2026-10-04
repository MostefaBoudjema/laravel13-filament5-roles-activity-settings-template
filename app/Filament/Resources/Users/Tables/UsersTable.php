<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')->label(__('Email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')->label(__('Roles'))
                    ->separator(', ')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label(__('Created at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Additional filters can be added later.
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
