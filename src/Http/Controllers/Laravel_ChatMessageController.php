<?php

namespace jwoodrow99\laravel_chat\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use jwoodrow99\laravel_chat\Models\Chat;
use jwoodrow99\laravel_chat\Models\Message;
use jwoodrow99\laravel_chat\Events\NewMessage;

class Laravel_ChatMessageController extends Controller
{
    public function index(Request $request, Chat $chat){
        $data = $request->all();

        $request->user()->Chats()->updateExistingPivot($chat->id, ['new_messages' => false]);

        if ($chat->users->contains($request->user())){
            if ($request->query('per_page')){
                return response([
                    'messages' => $chat->messages()->orderBy('id', 'desc')->paginate($request->query('per_page'))
                ]);
            } else {
                return response([
                    'messages' => $chat->messages()->orderBy('id', 'desc')->get()
                ]);
            }
        } else {
            return response([
                'message' => 'We can not find a record of you in this chat.'
            ], 401);
        }
    }

    public function create(Request $request, Chat $chat){
        $data = $request->all();

        if ($chat->users->contains($request->user())){
            $message = new Message([
                'message' => $data['message'],
                'chat_id' => $chat->id,
                'user_id' => $request->user()->id
            ]);

            try {
                $message->save();
            } catch (\Exception $e){
                response([
                    'message' => 'We could not send your message.'
                ], 500);
            }

            // Broadcast new message event
            event(new NewMessage($message));
            $chat->users()->where('id', '!=', $request->user()->id)->updateExistingPivot($chat->id, ['new_messages' => true]);

            return response([
                'message' => $message
            ]);
        } else {
            return response([
                'message' => 'Unauthorized.'
            ], 403);
        }
    }

    public function delete(Request $request, Chat $chat, Message $message){
        $data = $request->all();

        if ($message->chat->id == $chat->id){
            try {
                $message->delete();
            } catch (\Exception $e){
                return response([
                    'message' => 'This message could not be removed.'
                ]);
            }
        } else {
            return response([
                'message' => 'This message does not belong to the specified chat.'
            ]);
        }

        return response([
            'message' => 'Ok.'
        ]);
    }
}
