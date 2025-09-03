<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class DatabaseSetupController extends Controller
{
    /**
     * Check if database needs to be initialized and do it automatically
     */
    public function autoSetup()
    {
        try {
            // Check if database is already set up by looking for settings table
            if (!Schema::hasTable('settings')) {
                Log::info('Database not initialized, running migrations and seeding...');
                $this->runMigrations();
                $this->runSeeders();

                return response()->json([
                    'success' => true,
                    'message' => 'Database initialized successfully',
                    'action' => 'initialized'
                ]);
            }

            // Check if settings table has basic data
            $settingsCount = Setting::count();
            if ($settingsCount === 0) {
                Log::info('Settings table empty, running seeders...');
                $this->runSeeders();

                return response()->json([
                    'success' => true,
                    'message' => 'Database seeded successfully',
                    'action' => 'seeded'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Database already configured',
                'action' => 'already_configured'
            ]);

        } catch (\Exception $e) {
            Log::error('Database auto-setup failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Database setup failed: ' . $e->getMessage(),
                'action' => 'failed'
            ], 500);
        }
    }

    /**
     * Get database status information
     */
    public function status()
    {
        try {
            $status = [
                'database_connected' => $this->isDatabaseConnected(),
                'tables_exist' => $this->doTablesExist(),
                'settings_seeded' => $this->areSettingsSeeded(),
            ];

            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => 'Database status retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Database status check failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to check database status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset and reinitialize database (for development/testing)
     */
    public function resetDatabase()
    {
        try {
            Log::info('Resetting database...');

            // Run fresh migrations
            Artisan::call('migrate:fresh', ['--force' => true]);

            // Run seeders
            $this->runSeeders();

            return response()->json([
                'success' => true,
                'message' => 'Database reset and reinitialized successfully',
                'action' => 'reset'
            ]);

        } catch (\Exception $e) {
            Log::error('Database reset failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Database reset failed: ' . $e->getMessage(),
                'action' => 'failed'
            ], 500);
        }
    }

    /**
     * Check if database connection is working
     */
    private function isDatabaseConnected(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if essential tables exist
     */
    private function doTablesExist(): bool
    {
        try {
            return Schema::hasTable('settings') &&
                Schema::hasTable('users') &&
                Schema::hasTable('desa');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if settings table has been seeded
     */
    private function areSettingsSeeded(): bool
    {
        try {
            return Setting::count() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Run database migrations
     */
    private function runMigrations()
    {
        Artisan::call('migrate', ['--force' => true]);
        Log::info('Migrations completed: ' . Artisan::output());
    }

    /**
     * Run database seeders
     */
    private function runSeeders()
    {
        Artisan::call('db:seed', ['--force' => true]);
        Log::info('Seeding completed: ' . Artisan::output());
    }
}
