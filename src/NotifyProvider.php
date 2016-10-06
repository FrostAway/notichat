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
        $user_notify_count = 0;
        $normal_notify_count = 0;
        if (auth()->check()) {
            $user_notify = Notify::where('user_id', auth()->id())
                    ->whereIn('notify_type', ['group', 'chat'])
                    ->orderBy('updated_at', 'desc')
                    ->paginate(15);
            $user_notify_count = Notify::where('user_id', auth()->id())
                    ->whereIn('notify_type', ['group', 'chat'])
                    ->where('is_read', 0)
                    ->count();
            $normal_notify = Notify::where('user_id', auth()->id())
                    ->where('notify_type', 'normal')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(15);
            $normal_notify_count = Notify::where('user_id', auth()->id())
                    ->where('notify_type', 'normal')
                    ->where('is_read', 0)
                    ->count();
        }
        view()->share('user_notify', $user_notify);
        view()->share('user_notify_count', $user_notify_count);
        view()->share('normal_notify', $normal_notify);
        view()->share('normal_notify_count', $normal_notify_count);
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
