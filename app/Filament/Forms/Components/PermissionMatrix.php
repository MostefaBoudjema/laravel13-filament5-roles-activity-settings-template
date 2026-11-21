<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Spatie\Permission\Models\Permission;

class PermissionMatrix extends Field
{
    protected string $view = 'filament.forms.components.permission-matrix';

    /**
     * The ordered list of actions (X-axis columns).
     */
    protected array $actions = ['view_any', 'view', 'create', 'update', 'delete'];

    /**
     * Returns all permissions grouped by module.
     * Structure: [ 'student' => ['view_any' => 'view_any_student', 'view' => 'view_student', ...], ... ]
     */
    public function getModules(): array
    {
        $permissions = Permission::orderBy('name')->get()->pluck('name');

        $modules = [];
        foreach ($permissions as $permission) {
            $matched = false;
            foreach ($this->actions as $action) {
                if (str_starts_with($permission, $action . '_')) {
                    $module = substr($permission, strlen($action) + 1);
                    $modules[$module][$action] = $permission;
                    $matched = true;
                    break;
                }
            }

            // Skip standalone permissions — handled by getPagePermissions()
            if (! $matched) {
                continue;
            }
        }

        ksort($modules);
        return $modules;
    }

    /**
     * Returns standalone page/feature permissions that don't follow the action_module pattern.
     * Structure: [ 'access_my_line', 'access_queue_desk', ... ]
     */
    public function getPagePermissions(): array
    {
        $permissions = Permission::orderBy('name')->get()->pluck('name');

        $pagePerms = [];
        foreach ($permissions as $permission) {
            $matched = false;
            foreach ($this->actions as $action) {
                if (str_starts_with($permission, $action . '_')) {
                    $matched = true;
                    break;
                }
            }

            if (! $matched) {
                $pagePerms[] = $permission;
            }
        }

        return $pagePerms;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Return the currently selected permission names as a flat array.
     */
    public function getSelectedPermissions(): array
    {
        $state = $this->getState();

        if (is_null($state)) {
            return [];
        }

        if (is_string($state)) {
            // Could be JSON from the hidden input on round-trips
            $decoded = json_decode($state, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            return [];
        }

        if (is_array($state)) {
            return array_values(array_filter($state, fn($v) => is_string($v)));
        }

        if ($state instanceof \Illuminate\Support\Collection) {
            return $state->toArray();
        }

        return [];
    }
}
