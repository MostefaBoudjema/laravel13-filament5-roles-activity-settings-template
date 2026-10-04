<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthenticationActivity
{
    /**
     * Handle a successful login event.
     */
    public function handleLogin(Login $event): void
    {
        activity('authentication')
            ->causedBy($event->user)
            ->withProperties([
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard'      => $event->guard,
            ])
            ->log(__('User logged in'));
    }

    /**
     * Handle a logout event.
     */
    public function handleLogout(Logout $event): void
    {
        activity('authentication')
            ->causedBy($event->user)
            ->withProperties([
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard'      => $event->guard,
            ])
            ->log(__('User logged out'));
    }

    /**
     * Handle a failed login attempt.
     */
    public function handleFailed(Failed $event): void
    {
        activity('authentication')
            ->causedBy($event->user) // Could be null if user doesn't exist
            ->withProperties([
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard'      => $event->guard,
                'email'      => $event->credentials['email'] ?? null,
            ])
            ->log(__('Failed login attempt'));
    }
}
