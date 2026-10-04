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
        $currentYear = \App\Models\AcademicYear::where('is_current', true)->first();
        if (!$currentYear) {
            return;
        }

        $settingsToSeed = [
            'ministry_header.country_fr' => 'RÉPUBLIQUE ALGÉRIENNE DEMOCRATIQUE ET POPULAIRE',
            'ministry_header.ministry_fr' => "MINISTÈRE DE L'EDUCATION NATIONALE",
            'info.direction' => "DIRECTION DE L'EDUCATION WILAYA _ANNABA_ALGERIA",
            'info.name' => "ETABLISSEMENT D'EDUCATION ET D'ENSEIGNEMENT PRIVÉ",
            'info.type' => 'PRIMAIRE_MOYEN_SECONDAIRE « LAMRI BELAID »',
            'info.address' => "RUE AHCENE CHAOUCHE N°16 ET 18",
            'info.city' => "CITÉ L'ORANGERIE  ANNABA",
            'info.tel' => 'TEL :  038 43 43 25',
            'info.tel2' => '06.59.28.26.05',
            'info.email' => 'E-MAIL ecole.privee.lamri.belaid23000@gmail.com',
            'info.nrc' => "N°RC : 13A1859983 -00/23",
            'info.nif' => 'N° NIF : 163230104764163000000',
            'info.article' => "N°ARTICLE D'IMPOSITION : 23018711315",
            'info.compte' => 'N°COMPTE BDL : 00500206540022719001 7',
            'ministry_header.school' => 'مدرسة العمري بلعيد (عنابة)',
            'name' => 'Ecole privée Lamri Belaid - Annaba',
            'name_ar' => 'مؤسسة التربية و التعليم الخاصة العمري بلعيد',
            'manager_title' => 'LE DIRECTEUR',
        ];

        foreach ($settingsToSeed as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'academic_year_id' => $currentYear->id],
                ['value' => $value, 'type' => 'text', 'editable' => false]
            );
        }

        Setting::updateOrCreate(
            ['key' => 'restaurant_fees', 'academic_year_id' => $currentYear->id],
            ['value' => '5000', 'type' => 'number', 'editable' => true]
        );
        Setting::updateOrCreate(
            ['key' => 'INTENDANTE', 'academic_year_id' => $currentYear->id],
            ['value' => 'A.BOUSSEBAI', 'type' => 'text', 'editable' => true]
        );
        Setting::updateOrCreate(
            ['key' => 'DIRECTEUR', 'academic_year_id' => $currentYear->id],
            ['value' => 'A.AZIZI', 'type' => 'text', 'editable' => true]
        );
        Setting::updateOrCreate(
            ['key' => 'FONDATEUR', 'academic_year_id' => $currentYear->id],
            ['value' => 'K.LAMRI', 'type' => 'text', 'editable' => true]
        );
        Setting::updateOrCreate(
            ['key' => 'calculate_tva', 'academic_year_id' => $currentYear->id],
            ['value' => '0', 'type' => 'boolean', 'editable' => true]
        );
        Setting::updateOrCreate(
            ['key' => 'calculate_timbre', 'academic_year_id' => $currentYear->id],
            ['value' => '0', 'type' => 'boolean', 'editable' => true]
        );
    }
}
