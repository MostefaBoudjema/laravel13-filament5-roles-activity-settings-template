<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
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

        $setting_count = Setting::count();
        // Current Month dates
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        
        return [
            Stat::make(__('Setting'), $setting_count)
                ->description(__('Setting'))
                ->descriptionIcon('heroicon-s-cog-6-tooth')
                ->color('primary'),
        ];
    }
}
