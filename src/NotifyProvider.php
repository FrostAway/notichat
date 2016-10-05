<?php

namespace Devpa\NotiChat;

use Illuminate\Support\ServiceProvider;

class NotifyProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
 
            require __DIR__ . '/routes.php';

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->publishes([
            __DIR__ . '/config/notichat.php' => config_path('notichat.php')
                ], 'config');

        $this->publishes([
            __DIR__ . '/../clients' => public_path('clients'),
            __DIR__ . '/../server' => public_path('server'),
            __DIR__ . '/../assets' => public_path(''),
                ], 'assets');

        $this->publishes([
            __DIR__ . '/Controllers' => app_path('Http/Controllers')
                ], 'controllers');

        $this->publishes([
            __DIR__ . '/Events' => app_path('Events')
                ], 'events');

        $this->publishes([
            __DIR__ . '/Listeners' => app_path('Listeners')
                ], 'listeners');

        $this->publishes([
            __DIR__ . '/Models' => app_path('Models')
                ], 'models');

        $this->publishes([
            __DIR__ . '/resources/views' => 'resources/views'
                ], 'views');

        $this->publishes([
            __DIR__ . '/migrations' => database_path('migrations')
                ], 'migrations');
    }

}
