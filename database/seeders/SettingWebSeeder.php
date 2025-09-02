<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingWebSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = [
            [
                'context' => 'webhook_url',
                'content' => 'http://localhost:5678/webhook/74a19a60-ca35-4e4e-a28b-91800013ef50',
            ],
            [
                'context' => 'api_url',
                'content' => 'http://127.0.0.1:8000/api',
            ]
        ];

        foreach ($setting as $data) {
            Setting::create($data);
        }
    }
}
