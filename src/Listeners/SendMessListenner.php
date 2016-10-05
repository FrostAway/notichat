<?php

namespace App\Listeners;

use App\Events\SendMess;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Notify;
use App\Events\ChatNotify;

class SendMessListenner implements ShouldQueue {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendMess  $event
     * @return void
     */
    public function handle(SendMess $event) {
        $message = $event->message;
        $type = $event->type;
        if ($type == 'group') {
            $room = $event->room;
            $room_users = $room->users;
            foreach ($room_users as $ru) {
                $notify = Notify::where('user_id', $ru->id)->where('object_id', $message->room_id)->first();
                if (!$notify) {
                    $notify = Notify::create([
                                'user_id' => $ru->id,
                                'content' => '<strong>' . $room->name . '</strong> có tin nhắn mới!',
                                'object_id' => $room->id,
                                'notify_type' => 'group'
                    ]);
                } else {
                    if ($notify->user_id != $message->from_user_id) {
                        $notify->is_read = 0;
                        $notify->updated_at = date('Y-m-d H:i:s');
                        $notify->save();
                    }
                }
                if (!$notify->is_read && $ru->id != $message->from_user_id) {
                    event(new ChatNotify($notify));
                }
            }
        }
    }

}
