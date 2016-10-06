<?php

namespace Lamnv\NotiChat;

use Illuminate\Support\ServiceProvider;

class NotifyProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {

        require __DIR__ . '/routes.php';

        $user_notify = null;
        $normal_notify = null;
        if (auth()->check()) {
            $user_notify = Notify::where('user_id', auth()->id())
                    ->whereIn('notify_type', ['group', 'chat'])
                    ->where('is_read', 0)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(15);
            $normal_notify = Notify::where('user_id', auth()->id())
                    ->where('notify_type', 'normal')
                    ->where('id_read', 0)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(15);
        }
        view()->share('user_notify', $user_notify);
        view()->share('normal_notify', $normal_notify);
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
