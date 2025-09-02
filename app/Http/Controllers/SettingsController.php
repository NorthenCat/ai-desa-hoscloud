<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = [
            'api_url' => Setting::getApiUrl() ?? '',
            'webhook_url' => Setting::getWebhookUrl() ?? '',
        ];

        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'api_url' => 'nullable|string|url',
            'webhook_url' => 'nullable|string|url'
        ]);

        try {
            // Update API URL
            if ($request->has('api_url')) {
                Setting::setByContext('api_url', $request->input('api_url'));
            }

            // Update Webhook URL
            if ($request->has('webhook_url')) {
                Setting::setByContext('webhook_url', $request->input('webhook_url'));
            }

            return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('settings.index')->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }
}
