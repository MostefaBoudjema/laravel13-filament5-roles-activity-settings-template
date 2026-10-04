<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('Name'))
                    ->required(),
                TextInput::make('email')->label(__('Email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')->label(__('Password'))
                    ->password()
                    ->required(fn ($livewire): bool => $livewire instanceof CreateRecord)
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->minLength(8)
                    ->confirmed(),
                TextInput::make('password_confirmation')->label(__('Confirm Password'))
                    ->password()
                    ->required(fn ($livewire): bool => $livewire instanceof CreateRecord)
                    ->dehydrated(false)
                    ->minLength(8),
                Select::make('roles')->label(__('Roles'))
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
