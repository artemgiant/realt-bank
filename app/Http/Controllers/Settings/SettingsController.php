<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     * Redirects to the first available settings section (roles).
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('settings.roles.index');
    }
}
