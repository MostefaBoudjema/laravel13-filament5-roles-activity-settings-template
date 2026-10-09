<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL requires modifying the column to extend the enum values
        DB::statement("ALTER TABLE settings MODIFY COLUMN type ENUM('text', 'number', 'boolean', 'image') NOT NULL DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values (rows with 'image' must be removed first or this will fail)
        DB::statement("ALTER TABLE settings MODIFY COLUMN type ENUM('text', 'number', 'boolean') NOT NULL DEFAULT 'text'");
    }
};
