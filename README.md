# Laravel 13 & Filament v5 Starter Template

A production-ready starter template for Laravel 13 and Filament v5 with pre-configured role-based access control (RBAC), comprehensive user activity logging, and dynamic application settings.

---

## Features

* **Laravel 13 Framework:** Built on the latest Laravel framework standard.
* **Filament v5 Admin Panel:** Clean, responsive, and customizable administrative dashboard.
* **Role-Based Access Control (RBAC):** Powered by `spatie/laravel-permission` with full Filament resource management for Roles and Permissions.
* **Activity & Audit Logging:** Integrated with `spatie/laravel-activitylog` to track model changes and admin actions automatically.
* **Dynamic Application Settings:** Database-driven global application settings powered by `spatie/laravel-settings` with a dedicated Filament settings UI.
* **Pre-configured User Management:** User CRUD out of the box with role assignment, status toggles, and profile updates.

---

## System Requirements

* **PHP:** `^8.3` or `^8.4`
* **Composer:** `^2.2`
* **Database:** MySQL `^8.0` / PostgreSQL `^15.0` / SQLite `^3.35`
* **Node.js:** `^20.0` & NPM

---

## Installation & Setup

1. **Clone the repository:**
```bash
git clone https://github.com/mostefaboudjema/laravel13-filament5-roles-activity-settings-template.git
cd laravel13-filament5-roles-activity-settings-template

```


2. **Install PHP dependencies:**
```bash
composer install

```


3. **Install and build frontend assets:**
```bash
npm install
npm run build

```


4. **Environment Configuration:**
Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env

```


5. **Generate Application Key:**
```bash
php artisan key:generate

```


6. **Configure Database & Run Migrations:**
Update your database credentials in `.env`, then run:
```bash
php artisan migrate --seed

```


7. **Create a Filament Super Admin User:**
```bash
php artisan filament:user

```



---

## Core Packages

| Package | Purpose |
| --- | --- |
| `filament/filament` | Admin Panel Interface (v5) |
| `spatie/laravel-permission` | Roles and Permissions Management |
| `spatie/laravel-activitylog` | System-wide Audit and Activity Logs |
| `spatie/laravel-settings` | Application Settings Management |

---

## Folder Structure Highlights

```text
app/
├── Filament/
│   ├── Pages/
│   │   └── ManageSettings.php       # Dynamic App Settings Page
│   └── Resources/
│       ├── ActivityLogResource.php  # Audit Trail Interface
│       ├── RoleResource.php         # RBAC Role Management
│       └── UserResource.php         # User Management
├── Models/
│   └── User.php                     # Includes HasRoles & LogsActivity traits
└── Settings/
    └── GeneralSettings.php          # Class-based settings definition

```

---

## Configuration & Usage

### 1. Managing Roles & Permissions

Navigate to **User Management > Roles** in the Filament admin panel to create custom roles and assign granular permissions. Ensure your policies utilize `$user->can('permission_name')` or Spatie's permission helpers.

### 2. Monitoring Activity Logs

Models configured with `Spatie\Activitylog\Traits\LogsActivity` automatically log creation, updates, and deletions. View all system activities under **System > Activity Logs**.

### 3. Application Settings

Define new settings parameters inside `app/Settings/GeneralSettings.php`. You can access settings anywhere in your application:

```php
use App\Settings\GeneralSettings;

$siteName = app(GeneralSettings::class)->site_name;

```

---

## License

This project is open-sourced software licensed under the [MIT license](https://www.google.com/search?q=LICENSE).