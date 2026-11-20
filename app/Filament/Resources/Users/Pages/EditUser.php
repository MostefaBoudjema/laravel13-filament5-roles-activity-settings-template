<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Pages\BaseEditRecord;
use App\Filament\Resources\Users\UserResource;

class EditUser extends BaseEditRecord
{
    protected static string $resource = UserResource::class;
}
