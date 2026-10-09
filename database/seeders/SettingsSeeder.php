<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'INTENDANTE'],
            ['value' => 'A.BOUSSEBAI', 'type' => 'text', 'editable' => true]
        );
        Setting::updateOrCreate(
            ['key' => 'DIRECTEUR'],
            ['value' => 'A.AZIZI', 'type' => 'text', 'editable' => true]
        );
        Setting::updateOrCreate(
            ['key' => 'FONDATEUR'],
            ['value' => 'K.LAMRI', 'type' => 'text', 'editable' => true]
        );
        $settingsToSeed = [
            'ministry_header.country_fr' => "",
            'ministry_header.ministry_fr' => "",
            'info.direction' => "",
            'info.name' => "",
            'info.type' => "",
            'info.address' => "",
            'info.city' => "",
            'info.tel' => "",
            'info.tel2' => "",
            'info.email' => "",
            'info.nrc' => "",
            'info.nif' => "",
            'info.article' => "",
            'info.compte' => "",
            'ministry_header.school' => "",
            'name' => "",
            'name_ar' => "",
            'manager_title' => "",
            'logo' => "",
            'favicon' => 'public/favicon.ico',
        ];

        foreach ($settingsToSeed as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text', 'editable' => false]
            );
        }


    }
}
