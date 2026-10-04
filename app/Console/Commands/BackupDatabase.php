<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Ensure the directory exists
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        // We use mysqldump to back up the database
        // Password is passed via MYSQL_PWD env var to avoid exposing it in the process list
        $process = new Process(
            [
                'mysqldump',
                '-u', config('database.connections.mysql.username'),
                config('database.connections.mysql.database'),
            ],
            null,
            ['MYSQL_PWD' => config('database.connections.mysql.password')]
        );
        
        $process->setTimeout(300); // 5 minutes max

        $process->run();

        if ($process->isSuccessful()) {
            file_put_contents($path, $process->getOutput());
            $this->info("Database backup created successfully: {$path}");
            
            // Delete backups older than 30 days
            $this->cleanOldBackups();
        } else {
            $this->error('The backup process failed.');
            $this->error($process->getErrorOutput());
        }
    }

    protected function cleanOldBackups()
    {
        $files = Storage::files('backups');
        $thirtyDaysAgo = now()->subDays(30)->timestamp;
        
        foreach ($files as $file) {
            if (Storage::lastModified($file) < $thirtyDaysAgo) {
                Storage::delete($file);
                $this->info("Deleted old backup: {$file}");
            }
        }
    }
}
