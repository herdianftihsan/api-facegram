<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class AuthCOntroller extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', only: ['logout'])
        ];
    }
    //create fungction register user api
    public function Register(Request $request){
        $params = $request->validate([
           "full_name" => 'required',
           "bio" => 'required|max:100',
           "username" => 'required|min:3|unique:users|regex:/^[a-z0-9._]+$/',
           "password" => 'required|min:6',
           "is_private" => 'boolean'
        ]);

        $user = User::query()->create($params);
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'massage' => 'Register Success',
            'token' =>$token,
            'user' => $user
            
        ]);
    }

    public function login(Request $request){
        $params = $request->validate([
            "username" => 'string',
            "password" => 'string',
        ]);

        if (!Auth::attempt($params)) return response()->json([
             "message" => "Wrong username or password",
        ],401);

        $user = User::query()->find(Auth::user()->id);
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            "message" => "Login success",
            "token" => $token,
            "user" => $user,
        ]);
    }

    public function logout(){
        $user = Auth::user()->currentAccessToken()->delete();
        if (!$user) {
            return response()->json(["massage" => "Unauthenticated."], 401);
        }
        return response()->json(["massage" => "Logout success"],200);
    }
}
