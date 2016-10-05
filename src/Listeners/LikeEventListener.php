<?php

namespace App\Listeners;

use App\Events\LikeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Notify;
use App\Events\ChatNotify;

class LikeEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LikeEvent  $event
     * @return void
     */
    public function handle(LikeEvent $event)
    {
        $message = $event->message;
        $notify = Notify::create([
            'user_id' => $message['user_id'],
            'content' => '<strong>Bạn</strong> có thông báo mới',
            'object_id' => null,
            'notify_type' => 'normal'
        ]);
        event(new ChatNotify($notify));
    }
}
