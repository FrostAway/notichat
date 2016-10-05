<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\User;
use App\Events\LikeEvent;

class NotifyController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('notify.index', compact('users'));
    }
    
    public function makeNotify(Request $request){
        $num = $request->get('num');
        $user_id = $request->get('user_id');
        $user = User::find($user_id, ['name']);
        event(new LikeEvent(['user_id' => $user_id, 'name' => $user->name, 'message' => 'Message '.$num]));
    }

}
