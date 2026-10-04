<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages;
use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static ?int $navigationSort = 9;
    public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}
    public static function getModelLabel(): string
    {
        return __('Setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('view_page_settings');
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\Select::make('academic_year_id')
                    ->label(__('Academic Year'))
                    ->relationship('academicYear', 'name')
                    ->required()
                    ->default(fn () => \App\Models\AcademicYear::where('is_current', true)->value('id'))
                    ->disabled(),
                TextInput::make('key')->label(__('Key'))
                    ->required()
                    ->disabled()
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __($state) : null)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule, \Filament\Schemas\Components\Utilities\Get $get) => $rule->where('academic_year_id', $get('academic_year_id')))
                    ->maxLength(255),
                TextInput::make('value')
                    ->label(__('Value'))
                    ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('type') === 'boolean')
                    ->disabled(fn (?Setting $record) => $record ? ! $record->editable : false),
                \Filament\Forms\Components\Select::make('value')
                    ->label(__('Value'))
                    ->options([
                        '1' => 'TRUE',
                        '0' => 'FALSE',
                    ])
                    ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('type') !== 'boolean')
                    ->disabled(fn (?Setting $record) => $record ? ! $record->editable : false),
                // \Filament\Forms\Components\Toggle::make('editable')
                //     ->label(__('Editable'))
                //     ->default(true),
                \Filament\Forms\Components\Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'text' => __('Text'),
                        'number' => __('Number'),
                        'boolean' => __('Boolean'),
                    ])
                    ->default('text')
                    ->live()
                    ->required(),
            ])
            ->columns(3); // Forces the three components to sit side-by-side in a 3-column grid row
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academicYear.name')->label(__('Academic Year'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('key')->label(__('Key'))
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __($state) : null)
                    ->searchable(),
                \Filament\Tables\Columns\IconColumn::make('editable')->label(__('Editable'))
                    ->boolean(),
                TextColumn::make('type')->label(__('Type'))
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __(ucfirst($state)) : null)
                    ->searchable(),
                TextInputColumn::make('value')->label(__('Value'))
                    ->state(function ($record) {
                        if ($record->type === 'boolean') {
                            return $record->value == '1' ? 'TRUE' : 'FALSE';
                        }
                        return $record->value;
                    })
                    ->disabled(fn ($record) => ! $record->editable || $record->type === 'boolean')
                    ->updateStateUsing(function ($record, $state) {
                        $record->update(['value' => $state]);
                        \Filament\Notifications\Notification::make()
                            ->title(__('Saved successfully'))
                            ->success()
                            ->send();
                    })
                    ->searchable(),
                TextColumn::make('created_at')->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label(__('Academic Year'))
                    ->relationship('academicYear', 'name')
                    ->default(fn () => \App\Models\AcademicYear::where('is_current', true)->value('id')),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
