<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Message;

class SendMess extends Event implements ShouldBroadcast
{
    use SerializesModels;

    
    public $message;
    public $type;
    public $room;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message, $type = 'chat')
    {
        $this->message = $message;
        $this->type = $type;
        $this->room = null;
        if ($type == 'group') {
            $this->room = $message->room;
        }
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['send_mess'];
    }
}
