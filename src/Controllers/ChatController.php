<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Room;
use App\Models\Message;
use App\Models\Notify;
use App\Events\SendMess;
use App\Events\ChatNotify;
use Validator;

class ChatController extends Controller {

    public function index() {
        $exclude = [];
        if (auth()->check()) {
            $exclude = [auth()->id()];
        }
        $users = User::whereNotIn('id', $exclude)->get();
        $rooms = Room::with('users')->get();

        return view('chat.index', compact('users', 'rooms'));
    }

    /**
     * Send message
     * @param Request $request
     */
    public function sendMessage(Request $request) {
        $user = auth()->user();
        $chat_type = $request->has('type') ? $request->get('type') : 'chat';
        if ($chat_type == 'chat') {
            $valid = Validator::make($request->all(), [
                        'to_user' => 'required',
                        'message' => 'required'
            ]);
            if ($valid->fails()) {
                return response()->json([], 422);
            }
            $to_id = $request->get('to_user');
            $to_user = User::find($to_id, ['id', 'name']);
            $message = Message::create([
                        'message' => $request->get('message'),
                        'from_user_id' => $user->id,
                        'from_user_name' => $user->name,
                        'to_user_id' => $to_user->id,
                        'to_user_name' => $to_user->name
            ]);
            $notify = Notify::where(['user_id' => $message->to_user_id, 'object_id' => $message->from_user_id])->first();
            if (!$notify) {
                $notify = Notify::create([
                            'user_id' => $message->to_user_id,
                            'content' => '<strong>' . $message->from_user_name . '</strong> đã gửi cho bạn 1 tin nhắn!',
                            'object_id' => $message->from_user_id,
                            'notify_type' => 'chat'
                ]);
            } else {
                $notify->is_read = 0;
                $notify->updated_at = date('Y-m-d H:i:s');
                $notify->save();
            }
            event(new ChatNotify($notify));
        } else if ($chat_type == 'group') {
            $valid = Validator::make($request->all(), [
                        'room_id' => 'required',
                        'message' => 'required'
            ]);
            if ($valid->fails()) {
                return;
            }
            $room_id = $request->get('room_id');
            $room = Room::find($room_id);
            if (!$room) {
                return;
            }
            $mess_txt = $request->get('message');
            $message = Message::create([
                        'message' => $mess_txt,
                        'from_user_id' => $user->id,
                        'from_user_name' => $user->name,
                        'room_id' => $room_id
            ]);
            event(new SendMess($message, $chat_type));
        }
    }

    /**
     * Get messages
     * @param Request $request
     * @return type
     */
    public function getMessage(Request $request) {
        if ($request->has('room_id')) {
            $room_id = $request->get('room_id');
            $room = Room::find($room_id);
            return $room->messages;
        }
        if ($request->has('to_user') && $request->has('from_user')) {
            $to_user_id = $request->get('to_user');
            $from_user_id = $request->get('from_user');

            return Message::where(function ($query) use ($to_user_id, $from_user_id) {
                                $query->where('to_user_id', $to_user_id)
                                ->where('from_user_id', $from_user_id);
                            })->orWhere(function ($query) use ($to_user_id, $from_user_id) {
                                $query->where('to_user_id', $from_user_id)
                                ->where('from_user_id', $to_user_id);
                            })
                            ->get();
        }
        return;
    }

    /**
     * Create chat request to group
     * @param Request $request
     * @return type
     */
    public function initChatGroup(Request $request) {
        $valid = Validator::make($request->all(), [
                    'room_id' => 'required'
        ]);
        if ($valid->fails()) {
            return;
        }
        $user_id = auth()->id();
        $room_id = $request->get('room_id');
        $room = Room::find($room_id);
        event(new \App\Events\InitChatGroup($room, $user_id));
    }

    public function setReadNotify(Request $request) {
        $valid = Validator::make($request->all(), [
             'set_by' => 'required'
        ]);
        if ($valid->fails()) {
            return response()->json([], 402);
        }
        $set_by = $request->get('set_by');
        if ($set_by == 'note_id') {
            $notify_id = $request->get('note_id');
            $notify = Notify::find($notify_id);
            if (!$notify) {
                return response()->json([], 402);
            }
            $notify->is_read = 1;
            $notify->save();
        } else if ($set_by == 'user_id') {
            $user_id = $request->get('from_user_id');
            $notify_type = $request->get('notify_type');
            if ($notify_type == 'chat' || $notify_type == 'group') {
                Notify::where('user_id', $user_id)
                        ->where('object_id', $request->get('to_obj_id'))
                        ->update(['is_read' => 1]);
            }
        }
        return response()->json(['count' => Notify::where('user_id', auth()->id())->whereIn('notify_type', ['chat', 'group'])->where('is_read', 0)->count()]);
    }

    /**
     * Create room/group
     * @param Request $request
     * @return type
     */
    public function createRoom(Request $request) {
        $valid = Validator::make($request->all(), [
                    'room_name' => 'required|unique:rooms,name'
        ]);
        if ($valid->fails()) {
            return redirect()->back();
        }
        $room_name = $request->input('room_name');
        $room = Room::create([
                    'name' => $room_name,
                    'type' => 'room',
                    'owner_id' => auth()->id()
        ]);
        $room->users()->attach([auth()->id() => ['role_id' => 1]]);
        return redirect()->back();
    }

}
