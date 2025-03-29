<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = Message::where('recipient_id', auth()->id())->get();

        return response()->json(['message' => 'these are all the messages you recieved', $messages], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function send(User $user, Request $request)
    {
        $validate = $request->validate([
            'name' => 'string|nullable',
            'email' => 'email|nullable',
            'message' => 'required|string'
        ]);

        if (auth()->check()) {
            $sender = auth()->id();
            $name = auth()->user()->name;
            $email = auth()->user()->email;
        } else {
            $sender = null;
            if ($request->name && $request->email) {
                $name = $request->name;
                $email = $request->email;
            } else {
                return response()->json(['message' => 'if user not registered , require an email and name'], 400);
            }
        }

        $message = Message::create([
            'sender_id' => $sender,
            'recipient_id' => $user->id,
            'name' => $name,
            'email' => $email,
            'message' => $request->message
        ]);

        return response()->json(['message' => 'message created successfully', $message], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $message = Message::where('id', $id)->first();

        if (!$message) {
            return response()->json(['message' => 'message not found'], 404);
        }
        if ($message->recipient_id != auth()->id()) {
            return response()->json(['message' => 'cannot access a message that isnt yours'], 403);
        }
        return response()->json(['message' => 'this is the requested message', $message], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, $id)
    {
        $message = Message::where('id', $id)->first();

        if (!$message) {
            return response()->json(['message' => 'message not found'], 404);
        }
        if (auth()->id() != $message->sender_id && ($request->email != $message->email)) {
            return response()->json(['message' => 'you cannot update a message that isnt yours'], 403);
        }

        $validate = $request->validate([
            'message' => 'required|string'
        ]);

        $message->update([
            'message' => $request->message
        ]);

        return response()->json(['message' => 'message  updated successfully updated', $message], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request , $id)
    {
        $message = Message::where('id', $id)->first();

        if (!$message) {
            return response()->json(['message' => 'message not found'], 404);
        }

        if(auth()->id()){
            if( ($message->recipient_id !== auth()->id() ) && ($message->sender_id !== auth()->id())){
                return response()->json(['message' => 'cannot delete a message that you didnt recieve or send'], 403);
    
            }
        }
        else{
            if (!$request->has('email') || $message->email !== $request->email) {
                return response()->json(['message' => 'Cannot delete a message that you didn\'t send'], 403);
            }
        }

        $message->delete();

        return response()->json(['message' => 'message deleted successfully'], 200);
    }
}
