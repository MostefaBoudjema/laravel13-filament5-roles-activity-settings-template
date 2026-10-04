<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Permissions\Pages\CreatePermission;
use App\Filament\Resources\Permissions\Pages\EditPermission;
use App\Filament\Resources\Permissions\Pages\ListPermissions;
use App\Filament\Resources\Permissions\Schemas\PermissionForm;
use App\Filament\Resources\Permissions\Tables\PermissionsTable;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;
use BackedEnum;

class PermissionResource extends BaseResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::LockClosed;
    protected static ?string $model = Permission::class;
    protected static ?int $navigationSort = 0;
public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}
    public static function getModelLabel(): string
    {
        return __('Permission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Permissions');
    }

    public static function getNavigationLabel(): string
    {
        return __('Permissions');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Security');
    }

    public static function form(Schema $schema): Schema
    {
        return PermissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
