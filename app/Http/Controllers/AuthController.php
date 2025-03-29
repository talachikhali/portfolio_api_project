<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Mews\Purifier\Facades\Purifier;

class AuthController extends Controller
{
    public function login(Request $request){
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email' , $request->email)->first();

        if(!$user || Hash::check(Hash::make($request->password), $user->password)){
            return response()->json(['message' => 'invalid information'] , 401);
        }
        $token = $user->createToken($user->name . "-AuthToken")->plainTextToken;

        $data = [
            'name' => $user->name,
            'id' => $user->id,
            'email' => $user->email
        ];

        return response()->json([
            'user'=> $data,
            'token' => $token
        ], 200);
    }

    public function register(Request $request){
        $validate = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'bio' => 'required|string',
            'image' => 'required|image'
        ]);

        $imageName = $request->file('image')->store('images/users', 'public');
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bio' => Purifier::clean($validate['bio']),
            'image' => $imageName
        ]);

        $token = $user->createToken($user->name . "-AuthToken")->plainTextToken;

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'bio' => $user->bio,
            'image' => $user->image
        ];
        return response()->json(['message' => 'user created successfully' , 'user' => $data, 'token' => $token] , 200);
    }

    public function logout(Request $request){
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logged out']);
    }
}
