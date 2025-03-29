<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;
use Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'image' => $user->image,
                'bio' => $user->bio
            ];
        });
        return response()->json(['message' => 'these are all the users', $data], 200);
    }

    public function show($id){
        $user = User::where('id' , $id)->first();
        if(!$user){
            return response()->json(['message' => 'user doesnt exist'], 404);
        }
        return response()->json(['message' => 'this is the required user', $user], 200);
    }

    public function update(Request $request){
        $user = User::where('id' , auth()->id())->first();

        $validate = $request->validate([
            'name' => 'string',
            'image' => 'image',
            'password' => 'min:8|confirmed',
            'bio' => 'string'
        ]);

        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->store('/images/users' , 'public');
            if (Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
        } else {
            $imageName = $user->image;
        }

        $name = $request->name ? $request->name : $user->name;
        $bio = $request->bio ? Purifier::clean($validate['bio']) : $user->bio;
        $pw = $request->password ? $request->password : $user->password;

        $user->update([
            'name' => $name,
            'image' => $imageName,
            'bio' => $bio,
            'password' => $pw
        ]);

        return response()->json(['message' => 'profile updated successfully' , $user], 200);
    }

    public function destroy(){
        $user = User::where('id' , auth()->id())->first();

        if (Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }
        
        $user->delete();
        
        return response()->json(['message' => 'profile deleted'], 200);
    }
}

