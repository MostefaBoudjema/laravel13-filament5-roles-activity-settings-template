<?php

namespace Tests\Feature;

use App\Filament\Resources\Settings\Pages\CreateSetting;
use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Models\AcademicYear;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SettingResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private AcademicYear $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'super-admin']);

        $perm = Permission::firstOrCreate(['name' => 'view_page_settings']);

        $this->admin = User::factory()->create([
            'name'     => 'Super Admin',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->admin->assignRole('super-admin');
        $this->admin->givePermissionTo([$perm]);

        $this->academicYear = AcademicYear::factory()->create([
            'name'       => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date'   => '2026-06-30',
            'is_current' => true,
            'is_locked'  => false,
        ]);
    }

     public function test_can_edit_setting()
    {
        $setting = Setting::create([
            'academic_year_id' => $this->academicYear->id,
            'key' => 'existing_key',
            'value' => 'old_value',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(EditSetting::class, ['record' => $setting->id])
            ->fillForm([
                'value' => 'new_value',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'key' => 'existing_key',
            'value' => 'new_value',
        ]);
    }
}
