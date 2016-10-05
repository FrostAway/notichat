<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';
    protected $fillable = ['name', 'type', 'owner_id', 'status'];
    
    public function users(){
        return $this->belongsToMany('\App\User', 'users_rooms', 'room_id', 'user_id');
    }
    
    public function messages(){
        return $this->hasMany('\App\Models\Message', 'room_id', 'id');
    }
}
