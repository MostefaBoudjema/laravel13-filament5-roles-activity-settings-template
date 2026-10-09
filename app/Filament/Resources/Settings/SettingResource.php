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
use Filament\Tables\Columns\ImageColumn;
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

                TextInput::make('key')->label(__('Key'))
                    ->required()
                    ->disabled()
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __($state) : null)
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('value')
                    ->label(__('Value'))
                    ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('type') === 'boolean' || $get('type') === 'image')
                    ->disabled(fn (?Setting $record) => $record ? ! $record->editable : false),
                \Filament\Forms\Components\Select::make('value')
                    ->label(__('Value'))
                    ->options([
                        '1' => 'TRUE',
                        '0' => 'FALSE',
                    ])
                    ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('type') !== 'boolean')
                    ->disabled(fn (?Setting $record) => $record ? ! $record->editable : false),
                \Filament\Forms\Components\FileUpload::make('value')
                    ->label(__('Value'))
                    ->disk('public')
                    ->image()
                    ->imagePreviewHeight('120')
                    ->acceptedFileTypes([
                        'image/png',
                        'image/jpeg',
                        'image/gif',
                        'image/svg+xml',
                        'image/webp',
                        'image/x-icon',
                        'image/vnd.microsoft.icon',
                    ])
                    ->directory('settings')
                    ->validationMessages([
                        'mimetypes' => __('The file must be an image (PNG, JPG, GIF, SVG, WebP, ICO).'),
                    ])
                    ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('type') !== 'image')
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
                        'image' => __('Image'),
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
                TextColumn::make('key')->label(__('Key'))
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __($state) : null)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                \Filament\Tables\Columns\IconColumn::make('editable')->label(__('Editable'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('type')->label(__('Type'))
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __(ucfirst($state)) : null)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('value')->label(__('Value'))
                        ->state(function ($record) {
                            if ($record->type === 'boolean') {
                                return $record->value == '1' ? 'TRUE' : 'FALSE';
                            }
                            if ($record->type === 'image') {
                                return null; // image rows show the ImageColumn instead
                            }
                            return $record->value;
                        })
                        ->disabled(fn ($record) => ! $record->editable || $record->type === 'boolean' || $record->type === 'image')
                        ->updateStateUsing(function ($record, $state) {
                            if ($record->type === 'image') {
                                return; // prevent accidental text update on image rows
                            }
                            $record->update(['value' => $state]);
                            \Filament\Notifications\Notification::make()
                                ->title(__('Saved successfully'))
                                ->success()
                                ->send();
                        })
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: false),
                ImageColumn::make('value_image')
                    ->label(__('Value'))
                    ->height(48)
                    ->getStateUsing(function ($record) {
                        if ($record->type !== 'image' || ! $record->value) {
                            return null;
                        }
                        // Generate a URL relative to the current request host,
                        // avoiding APP_URL / localhost mis-configuration on VPS.
                        return url(\Illuminate\Support\Facades\Storage::disk('public')->url($record->value));
                    })
                    ->visible(fn ($record) => true) // always rendered; null state = blank cell
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')->label(__('Created at'))
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('Updated at'))
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

            ])
            ->defaultPaginationPageOption(30)
            ->defaultSort('updated_at', 'desc')
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
