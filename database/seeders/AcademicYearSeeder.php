<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::firstOrCreate(
            ['name' => '2026-2027'],
            [
                'start_date' => '2026-09-01',
                'end_date' => '2027-06-30',
                'is_current' => true,
                'is_locked' => false,
                ]
            );
            AcademicYear::firstOrCreate(
                ['name' => '2026-2027'],
                [
                    'start_date' => '2026-09-01',
                    'end_date' => '2027-06-30',
                    'is_current' => false,
                    'is_locked' => false,
                ]
            );
    }
}
