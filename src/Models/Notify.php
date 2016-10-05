<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    protected $table = 'notification';
    protected $fillable = ['user_id', 'title', 'content', 'object_id', 'notify_type', 'is_read', 'read_at'];  
}
