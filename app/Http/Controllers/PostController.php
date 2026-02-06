<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // public static function middleware(){
    //     return [
    //         new Middleware('auth:sanctum')
    //     ];
    // }

    public function index(Request $request) {
        $params = $request->validate([
            'page' => 'sometimes|integer|min:0',
            'size' => 'sometimes|integer|min:1',
        ]);

        $page =$params['page'] ?? 0;
        $size =$params['size'] ?? 10;
        

        $offset = $page * $size;
        return response()->json([
             'page' => $page,
             'size' => $size,
             "posts" =>[Post::offset($offset)->limit($size)->with('user', 'attachments')->get()],
        ]);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'caption' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,webp,png,gif'
        ]);

        $post = Auth::user()->posts()->create([
            'caption' => $validated['caption'],
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('posts');

                $post->postAttachments()->create([
                    'storage_path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Create post success'
        ], 201);
    }

    public function destroy($id){
        if (!($post = Post::query()->find($id))) return response()->json([
             "message"=> "Post not found"
        ],404); 

        if ($post != Auth::user()) {
            return response()->json([
                "message"=> "Forbidden access"
            ],403);
        }

        $post->delete();
        $post->postAttachments()->delete();

        return response(status:204);
    }
}
