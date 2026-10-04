<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('copy_from_previous_year')
                ->label(__('Copy from Previous Year'))
                ->icon('heroicon-s-document-duplicate')
                ->action(function () {
                    $currentYear = \App\Models\AcademicYear::where('is_current', true)->first();
                    if (!$currentYear) {
                        \Filament\Notifications\Notification::make()->title(__('Current academic year not found'))->danger()->send();
                        return;
                    }
                    $previousYear = \App\Models\AcademicYear::where('start_date', '<', $currentYear->start_date)->orderBy('start_date', 'desc')->first();
                    if (!$previousYear) {
                        \Filament\Notifications\Notification::make()->title(__('Previous academic year not found'))->danger()->send();
                        return;
                    }

                    $previousSettings = \App\Models\Setting::where('academic_year_id', $previousYear->id)->get();
                    if ($previousSettings->isEmpty()) {
                        \Filament\Notifications\Notification::make()->title(__('No settings found for previous year'))->warning()->send();
                        return;
                    }

                    $count = 0;
                    foreach ($previousSettings as $setting) {
                        $created = \App\Models\Setting::firstOrCreate([
                            'key' => $setting->key,
                            'academic_year_id' => $currentYear->id,
                        ], [
                            'value' => $setting->value,
                            'type' => $setting->type,
                        ]);
                        if ($created->wasRecentlyCreated) {
                            $count++;
                        }
                    }
                    
                    \Filament\Notifications\Notification::make()
                        ->title(__(':count Settings copied successfully', ['count' => $count]))
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('info'),
            Actions\Action::make('export_db')
                ->label('Export Database')
                ->icon('heroicon-s-arrow-down-tray')
                ->action(function () {
                    $excluded = ['cache', 'jobs', 'migrations', 'failed_jobs', 'cache_locks', 'job_batches', 'sessions', 'password_reset_tokens', 'personal_access_tokens'];
                    $tables = collect(DB::select('SHOW TABLES'))
                        ->map(function ($row) {
                            return (array) $row;
                        })
                        ->flatten();
                    
                    $sql = "-- Database Export\n-- Generated: " . now()->toDateTimeString() . "\n\n";
                    $pdo = DB::connection()->getPdo();
                    
                    foreach ($tables as $table) {
                        if (in_array($table, $excluded)) {
                            continue;
                        }
                        
                        $rows = DB::table($table)->get();
                        if ($rows->isEmpty()) {
                            continue;
                        }
                        
                        $sql .= "-- Table: {$table}\n";
                        foreach ($rows as $row) {
                            $rowArray = (array) $row;
                            $columns = array_map(function($col) {
                                return "`$col`";
                            }, array_keys($rowArray));
                            
                            $values = array_map(function($val) use ($pdo) {
                                if (is_null($val)) return 'NULL';
                                return $pdo->quote($val);
                            }, array_values($rowArray));
                            
                            $sql .= "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        }
                        $sql .= "\n";
                    }
                    
                    $filename = 'base_de_donne_' . now()->format('Y-m-d_H-i-s') . '.sql';
                    $tempPath = storage_path('app/' . $filename);
                    file_put_contents($tempPath, $sql);
                    return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
                })
                ->requiresConfirmation(),
        ];
    }
}
