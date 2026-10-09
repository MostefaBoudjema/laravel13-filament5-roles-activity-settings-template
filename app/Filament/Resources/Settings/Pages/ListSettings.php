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
