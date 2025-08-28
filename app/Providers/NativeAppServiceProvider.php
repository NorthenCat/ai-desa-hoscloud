<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '1G',
            'upload_max_filesize' => '1G',
            'post_max_size' => '1G',
            'max_execution_time' => '0',
            'max_input_time' => '0',
            'default_socket_timeout' => '0',
            'max_file_uploads' => 1000,
        ];
    }
}
