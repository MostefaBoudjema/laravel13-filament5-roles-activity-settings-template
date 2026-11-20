<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Pages\BaseEditRecord;
use App\Filament\Resources\Settings\SettingResource;
use Filament\Actions;

class EditSetting extends BaseEditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
