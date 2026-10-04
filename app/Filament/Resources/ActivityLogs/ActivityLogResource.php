<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends BaseResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;
    protected static ?string $model = Activity::class;
    protected static ?int $navigationSort = 5;
    protected static ?string $recordTitleAttribute = 'description';
public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}
    public static function getModelLabel(): string
    {
        return __('Activity Log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Activity Logs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Activity Logs');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Security');
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
    }

    /**
     * Only users with 'view_any_activity_log' permission can access this resource.
     * Since super-admin bypasses Gate checks, they always have access.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_activity_log') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
        ];
    }
}
