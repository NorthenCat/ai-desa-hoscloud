<?php

namespace App\Http\Controllers\Traits;

use App\Models\Setting;

trait HasApiUrl
{
    /**
     * Get API URL from database settings with fallback to config
     */
    private function getApiUrl(): string
    {
        // First try to get from database
        $apiUrl = Setting::getApiUrl();

        // Fallback to config if not found in database
        if (!$apiUrl) {
            $apiUrl = config('app.api_url');
        }

        // Final fallback to default URL
        if (!$apiUrl) {
            $apiUrl = config('app.url', 'http://127.0.0.1:8000') . '/api';
        }

        return $apiUrl;
    }
}
