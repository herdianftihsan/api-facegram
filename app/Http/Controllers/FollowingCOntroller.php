<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowingCOntroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($username)
    {
        $user = User::where('username',$username)->first();
        if (!$user) {
           return response()->json(["message"=> "User not found" ],422);
        }
        $following = Follow::where('following_id',$user->id)->where('follower_id',Auth::id())->first();
        if (!$following) {
            return response()->json(["message" => "User not found"], 422);
        }

        return response()->json([
            'following' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$username)
    {
        if(!$user = User::where('username',$username)->first()) {
            return response()->json(["message"=> "User not found" ],404);
        }

        if ($user->id === Auth::id()) {
            return response()->json(["message" => "You are not allowed to follow yourself"],422);
        }

        $isPrivate = $user->is_private ? 0 : 1;

        if (Follow::where('follower_id',Auth::id())->where('following_id',$user->id)->exists()) {
           return response()->json([
             "message" => "You are already followed",
            "status" => $isPrivate ?  "following" : "requested" ,
           ],422);
        }

        $isPrivate = $user->is_private ? 0 :1;  

         Follow::create([
            'follower_id' => Auth::id(),
            'following_id' => $user->id,
            'is_accepted' => $isPrivate ? 1 : 0,
        ]);

        return response()->json([
            "message" => "Follow success",
            "status"=> $isPrivate ? "following" :  "requested" ,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $username)
    {
        if (!$user = User::where('username',$username)->first()) {
            return response()->json(["message" => "User not found"], 404);
        }

        $follower = Follow::where('follower_id', Auth::id())->where('following_id',$user->id)->first();

        if (!$follower) {
            return response()->json(["message" => "User not found"], 404);
        }

        return response()->json([
            'follower'=>$user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($username)
    {
        if (!$user = User::where('username',$username)->first()) {
            return response()->json(["message" => "User not found"], 404);
        }


        $accFollower = Follow::where('following_id',$user->id)->where('follower_id',Auth::id())->first();

        if (!$accFollower) {
            return response()->json(["message" => "The user is not following you"], 422);
        }

        if ($accFollower->is_accepted == 1) {
           return response() ->json(["message" => "Follow request is already accepted"],422);
        } 

        $accFollower->update([
            'is_accepted' => 1,
        ]);
        
        return response()->json([
            "message" => "Follow request accepted"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $username)
    {
        if(!$user = User::where('username',$username)->first()){
            return response()->json(["message" => "User not found"], 404);
        }

        $following = Follow::where('following_id',$user->id)->where('follower_id',Auth::id())->first();

        if (!$following) {
            return response()->json(["message" => "You are not following the user"],422);
        }

       $following->delete();
       return response(status:204);
    }
}
