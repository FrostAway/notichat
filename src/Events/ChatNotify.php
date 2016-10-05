<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\Notify;

class ChatNotify extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $notify;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Notify $notify)
    {
        $this->notify = $notify;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['notify-chat-'.$this->notify->user_id];
    }
}
