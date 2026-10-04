<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class DashboardWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        $academicYearId = $this->filters['academic_year_id'] ?? AcademicYear::current()->value('id');

        // Current Month dates
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        
        return [
            
        ];
    }
}
