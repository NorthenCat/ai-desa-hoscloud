<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'context',
        'content'
    ];

    /**
     * Get setting value by context
     */
    public static function getByContext(string $context): ?string
    {
        $setting = self::where('context', $context)->first();
        return $setting ? $setting->content : null;
    }

    /**
     * Set setting value by context
     */
    public static function setByContext(string $context, string $content): void
    {
        self::updateOrCreate(
            ['context' => $context],
            ['content' => $content]
        );
    }

    /**
     * Get API URL from settings
     */
    public static function getApiUrl(): ?string
    {
        return self::getByContext('api_url');
    }

    /**
     * Get webhook URL from settings
     */
    public static function getWebhookUrl(): ?string
    {
        return self::getByContext('webhook_url');
    }
}
