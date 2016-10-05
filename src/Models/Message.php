<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $fillable = ['message', 'from_user_id', 'from_user_name', 'room_id', 'to_user_id', 'to_user_name'];
    
    public function room(){
        return $this->belongsTo('\App\Models\Room', 'room_id', 'id');
    }
}
