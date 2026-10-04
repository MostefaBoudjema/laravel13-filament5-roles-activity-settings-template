<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardWidget;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.dashboard';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('Dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return __('Dashboard');
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('view_page_stats_dashboard');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardWidget::class,
        ];
    }
}
