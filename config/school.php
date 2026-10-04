<?php

return [

    /*
    |--------------------------------------------------------------------------
    | School Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the various settings used by the school system.
    | These values serve as defaults and can be overridden from the settings
    | table at runtime (see AppServiceProvider).
    |
    */

    'manager' => env('SCHOOL_MANAGER', 'Le Directeur'), 
    'manager_ar' => env('SCHOOL_MANAGER_AR', 'المسير'),

    'info' => [
        'direction' => "DIRECTION DE L'EDUCATION WILAYA _ANNABA_ALGERIA",
        'name'      => "ETABLISSEMENT D'EDUCATION ET D'ENSEIGNEMENT PRIVÉ",
        'type'      => 'PRIMAIRE_MOYEN_SECONDAIRE « LAMRI BELAID »',
        'address'   => "RUE AHCENE CHAOUCHE N°16 ET 18",
        'city'      => "CITÉ L'ORANGERIE  ANNABA",
        'tel'       => 'TEL :  038 43 43 25',
        'tel2'      => '06.59.28.26.05',
        'email'     => 'E-MAIL ecole.privee.lamri.belaid23000@gmail.com',
        'nrc'       => "N°RC : 13A1859983 -00/23",
        'nif'       => 'N° NIF : 163230104764163000000',
        'article'   => "N°ARTICLE D'IMPOSITION : 23018711315",
        'compte'    => 'N°COMPTE BDL : 00500206540022719001 7',
    ],

    'manager_title' => 'LE DIRECTEUR',

    'ministry_header' => [
        'country' => 'الجمهورية الجزائرية الديمقراطية الشعبية',
        'ministry' => 'وزارة التربية الوطنية',
        'country_fr' => 'RÉPUBLIQUE ALGÉRIENNE DEMOCRATIQUE ET POPULAIRE',
        'ministry_fr' => "MINISTÈRE DE L'EDUCATION NATIONALE",
    ],

    'months' => [
        'SEPTEMBRE', 'OCTOBRE', 'NOVEMBRE', 'DÉCEMBRE',
        'JANVIER', 'FÉVRIER', 'MARS', 'AVRIL', 'MAI', 'JUIN',
    ],

];
