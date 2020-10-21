<?php

namespace jwoodrow99\laravel_chat\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use jwoodrow99\laravel_chat\Models\Chat;
use App\Models\User;

class Laravel_ChatChatController extends Controller
{

    // Function used to test controller functionality
    public function test(Request  $request){
        $data = $request->all();

        return response([
            'message' => 'test',
            'request' => $data,
            'data' => [

            ]
        ]);
    }

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
    public function read(Request $request, Chat $chat){
        $data = $request->all();

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
    public function show(Request $request, Chat $chat){
        $data = $request->all();

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
    public function destroy(Request $request, Chat $chat){
        $data = $request->all();

        $chat->delete();

        return response([
            'message' => 'Ok.'
        ]);
    }

    // List all users in chat
    public function users(Request $request, Chat $chat){
        $data = $request->all();

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
    public function addUsers(Request $request, Chat $chat){
        $data = $request->all();

        $validUsers = [];
        $invalidUsers = [];

        foreach ($data['users'] as $user){
            try {
                $user = User::findOrFail($user);
                array_push($validUsers, $user);
            } catch (\Exception $e) {
                array_push($invalidUsers, $user);
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
    public function removeUser(Request $request, Chat $chat, User $user){
        $data = $request->all();

        if ($user->id != $chat->user_id){
            $chat->users()->detach($user);
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
    public function syncUsers(Request $request, Chat $chat){
        $data = $request->all();

        if (!in_array($chat->owner_id, $data['users'])){
            try {
                $chat->users()->sync($data['users']);
            } catch (\Exception $e) {
                return response([
                    'message' => 'Unable to sync users to chat.'
                ], 500);
            }
        } else {
            return response([
                'message' => 'You cannot include chat owner in sync.'
            ], 403);
        }

        return response([
            'message' => 'Ok.',
            'users' => $chat->users
        ]);
    }
}
