<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Activity Details'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('log_name')
                                    ->label(__('Log Name')),
                                TextInput::make('event')
                                    ->label(__('Event')),
                                TextInput::make('created_at')
                                    ->label(__('When')),
                            ]),
                        TextInput::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Subjects & Actors'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('causer_type')
                                    ->label(__('Causer Type'))
                                    ->formatStateUsing(function ($record) {
                                        if (! $record?->causer) {
                                            return '-';
                                        }

                                        $name = $record->causer->name ?? $record->causer->full_name ?? ($record->causer->first_name ? trim("{$record->causer->first_name} {$record->causer->last_name}") : null) ?? $record->causer_type;

                                        return "{$name} (#{$record->causer_id})";
                                    }),
                                TextInput::make('subject_type')
                                    ->label(__('Subject Type'))
                                    ->formatStateUsing(fn ($record) => $record?->subject_type ? "{$record->subject_type} (#{$record->subject_id})" : '-'),
                            ]),
                    ]),
                Section::make(__('Properties & Changes'))
                    ->schema([
                        Textarea::make('properties')
                            ->label(__('Properties (JSON)'))
                            ->rows(14)
                            ->formatStateUsing(function ($state, $record) {
                                $props = $record?->properties;
                                if ($props instanceof \Illuminate\Support\Collection) {
                                    $props = $props->toArray();
                                }

                                if (is_array($props)) {
                                    $studentId = $props['student_id'] ?? $props['old']['student_id'] ?? $props['attributes']['student_id'] ?? null;
                                    if ($studentId && !isset($props['student'])) {
                                        $student = \App\Models\Student::withTrashed()->find($studentId);
                                        if ($student) {
                                            $props['student'] = [
                                                'first_name_ar' => $student->first_name_ar,
                                                'last_name_ar'  => $student->last_name_ar,
                                            ];
                                        }
                                    }

                                    return json_encode($props, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                }

                                return (string) $props;
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
