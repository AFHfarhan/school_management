<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Carbon;

class TeacherAuthEventListener
{
    /**
     * Handle user login events.
     */
    public function handleLogin(Login $event)
    {
        $user = $event->user;
        // only handle teachers
        if (method_exists($user, 'getAuthIdentifierName') && $user->getTable() === 'teachers') {
            $tdata = is_array($user->data) ? $user->data : json_decode($user->data, true);
            $tdata['latest_login'] = now()->format('Y-m-d H:i:s');
            $user->data = $tdata;
            $user->save();
        }
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event)
    {
        $user = $event->user;
        if (! $user) {
            return;
        }
        if (method_exists($user, 'getAuthIdentifierName') && $user->getTable() === 'teachers') {
            $tdata = is_array($user->data) ? $user->data : json_decode($user->data, true);
            $tz = $tdata['timezone'] ?? null;
            $tz = $tz ?: config('app.timezone');
            $tdata['latest_logout'] =  Carbon::now($tz)->format('Y-m-d H:i:s');
            $user->data = $tdata;
            $user->save();
        }
    }
}
