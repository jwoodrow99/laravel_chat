<?php

namespace jwoodrow99\laravel_chat\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use jwoodrow99\laravel_chat\Models\Chat;

class Laravel_ChatChatController extends Controller
{
    // Get all chats that the requesting user belongs to
    public function index(Request $request){
        $data = $request->all();

        return response([
            'chats' => $request->user()->chats
        ]);
    }

    // Get all chats that exist
    public function all(Request $request){
        $data = $request->all();

        return response([
            'chats' => Chat::all()
        ]);
    }

    // Mark chat as read by requesting user
    public function read(Request $request, $chat_id){
        $data = $request->all();

        try {
            $chat = Chat::findOrFail($chat_id);
        } catch (\Exception $e){
            return response([
                'msg' => 'Specified chat was not found'
            ], 404);
        }

        if ($chat->users->contains($request->user())){
            $request->user()->Chats()->updateExistingPivot($chat->id, ['new_messages' => false]);
            return response([
                'chat' => $chat
            ]);
        }
        return response([
            'message' => 'We can not find a record of you in this chat.'
        ], 404);
    }

    // Show an individual chat
    public function show(Request $request, $chat_id){
        $data = $request->all();

        try {
            $chat = Chat::findOrFail($chat_id);
        } catch (\Exception $e){
            return response([
                'msg' => 'Specified chat was not found'
            ], 404);
        }

        if ($chat->users->contains($request->user())){
            return response([
                'chat' => $chat
            ]);
        } else {
            return response([
                'message' => 'We can not find a record of you in this chat.'
            ], 401);
        }
    }

    // Create a new chat
    public function create(Request $request){
        $data = $request->all();

        $this->validate($request, [
            'name' => 'required|unique:chats,name'
        ]);

        $data['owner_id'] = $request->user()->id;

        $chat = Chat::create($data);
        $chat->users()->attach($request->user());

        return response([
            'chat' => $chat
        ]);
    }

    // Destroy an existing chat
    public function destroy(Request $request, $chat_id){
        $data = $request->all();

        try {
            $chat = Chat::findOrFail($chat_id);
        } catch (\Exception $e){
            return response([
                'msg' => 'Specified chat was not found'
            ], 404);
        }

        $chat->delete();

        return response([
            'message' => 'Ok.'
        ]);
    }

    // List all users in chat
    public function users(Request $request, $chat_id){
        $data = $request->all();

        try {
            $chat = Chat::findOrFail($chat_id);
        } catch (\Exception $e){
            return response([
                'msg' => 'Specified chat was not found'
            ], 404);
        }

        if ($chat->users->contains($request->user())) {
            return response([
                'users' => $chat->users
            ]);
        } else {
            return response([
                'message' => 'We can not find a record of you in this chat.'
            ], 401);
        }
    }

    // Add users to chat
    public function addUsers(Request $request, $chat_id){
        $data = $request->all();

        try {
            $chat = Chat::findOrFail($chat_id);
        } catch (\Exception $e){
            return response([
                'msg' => 'Specified chat was not found'
            ], 404);
        }

        $validUsers = [];
        $invalidUsers = [];

        foreach ($data['users'] as $user){
            try {
                $user = User::findOrFail($user);
                array_push($validUsers, $user->id);
            } catch (\Exception $e) {
                array_push($invalidUsers, $user->id);
            }
        }

        $chat->users()->syncWithoutDetaching($validUsers);

        return response([
            'users' => $chat->users,
            'added_users', $validUsers,
            'ignored_users' => $invalidUsers,
        ]);
    }

    // Remove a single user from chat
    public function removeUser(Request $request, $chat_id, $user_id){
        $data = $request->all();

        try {
            $chat = Chat::findOrFail($chat_id);
        } catch (\Exception $e){
            return response([
                'msg' => 'Specified chat was not found'
            ], 404);
        }

        try {
            $user = User::findOrFail($user_id);
        } catch (\Exception $e){
            return response([
                'msg' => 'Specified user was not found'
            ], 404);
        }

        if ($user->id != $chat->owner_id){
            $chat->users()->detach($user->id);
        } else {
            return response([
                'message' => 'You cannot remove chat owner from chat.'
            ], 403);
        }

        return response([
            'message' => 'Ok.',
            'users' => $chat->users
        ]);
    }

    // Sync all users in request array
    //  - Remove users in chat, who are not in request array
    //  - Add users who are in request array but not in chat
    public function syncUsers(Request $request, $chat_id){
        $data = $request->all();

        $users = $data['users'];

        try {
            $chat = Chat::findOrFail($chat_id);
        } catch (\Exception $e){
            return response([
                'msg' => 'Specified chat was not found'
            ], 404);
        }

        if (!in_array($chat->owner_id, $users)){
            array_push($users, (string) $chat->owner_id);
        }

        try {
            $chat->users()->sync($users);
        } catch (\Exception $e) {
            return response([
                'message' => 'Unable to sync users to chat.'
            ], 500);
        }

        return response([
            'message' => 'Ok.',
            'users' => $chat->users
        ]);
    }
}
