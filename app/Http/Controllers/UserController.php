<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $following = Follow::where('follower_id', Auth::id())->pluck('following_id');
        $user = User::where('id', '!=', Auth::id())->whereNotIn('id', $following)->get();

        return response()->json([
            'users' => $user,
            'test' => 'adhjvdbfj'
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $username)
    {
        if (!$user = User::where('username', $username)->first()) {
            return response()->json(['massage' => "User not found"], 404);
        }
        $isAccount = $user != Auth::user() ? false : true;

        $follow = null;

        if (Auth::check() && !$isAccount) {
            $follow = Follow::where('follower_id', Auth::id())->where('following_id', $user->id)->first();
        }

        $followingStatus = "not-following";
        if ($follow) {
            $followingStatus = $follow->is_accepted ? "following" : "requested";
        }




        return response()->json([
            'id' => $user->id,
            'full_name' => $user->full_name,
            'username' => $user->username,
            'bio' => $user->bio,
            'is_private' => $user->is_private,
            "created_at" => $user->created_at,
            "is_your_account" => $isAccount,
            "following_status" => $followingStatus,
            "posts_count" => Post::where('user_id', $user->id)->count(),
            "followers_count" => Follow::where('following_id', $user->id)->where('is_accepted', 1)->count(),
            "following_count" => Follow::where('follower_id', $user->id)->where('is_accepted', 1)->count(),
            "posts" => Post::with('attachments')->where('user_id',$user->id)->latest()->get(),
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
