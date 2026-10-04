<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('Name'))
                    ->required()
                    ->placeholder('2025-2026'),
                Grid::make(2)
                    ->schema([
                        DatePicker::make('start_date')->label(__('Start Date'))
                            ->required(),
                        DatePicker::make('end_date')->label(__('End Date'))
                            ->required(),
                    ]),
                Grid::make(2)
                    ->schema([
                        Toggle::make('is_current')->label(__('Current Year'))
                            ->default(false),
                        Toggle::make('is_locked')->label(__('Locked'))
                            ->default(false),
                    ]),
            ]);
    }
}
