<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Room;

class InitChatGroup extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $room;
    public $user_ids;
    public $curr_user_id;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Room $room, $user_id)
    {
        $this->room = $room;
        $user_ids = $room->users()->lists('user_id')->all();
        if (!in_array($user_id, $user_ids)) {
            array_push($user_ids, $user_id);
        }
        $this->user_ids = $user_ids;
        $this->curr_user_id = $user_id;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['init-chat-group'];
    }
}
