<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label(__('Log'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'authentication' => 'info',
                        'users'          => 'danger',
                        'roles'          => 'gray',
                        'permissions'    => 'gray',
                        default          => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('description')
                    ->label(__('Description'))
                    // ->formatStateUsing(fn (?string $state): ?string => $state ? __($state) : null)
                    ->wrap()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('causer.name')
                    ->label(__('Performed by'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('subject_type')
                    ->label(__('Subject Type'))
                    ->wrap()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('properties.ip')
                    ->label(__('IP Address'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('properties.user_agent')
                    ->label(__('User Agent'))
                    ->wrap()
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('properties')
                    ->label(__('Details'))
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state, $record) {
                        $props = $record->properties;
                        if ($props instanceof \Illuminate\Support\Collection) {
                            $props = $props->toArray();
                        }
                        return is_array($props) 
                            ? json_encode($props, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) 
                            : (string) $props;
                    })
                    ->limit(120),
                TextColumn::make('created_at')
                    ->label(__('When'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label(__('Log name'))
                    ->options([
                        'authentication' => __('Authentication'),
                        'users'          => __('Users'),
                        'roles'          => __('Roles'),
                        'permissions'    => __('Permissions'),
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
