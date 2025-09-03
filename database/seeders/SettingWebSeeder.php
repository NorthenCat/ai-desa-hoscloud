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
                'content' => 'http://103.49.239.117:5678/webhook/74a19a60-ca35-4e4e-a28b-91800013ef50',
            ],
            [
                'context' => 'api_url',
                'content' => 'http://103.49.239.117:8010/api',
            ]
        ];

        foreach ($setting as $data) {
            Setting::firstOrCreate(['context' => $data['context']], $data);
        }
    }
}
