<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PostController extends Controller
{
    //
    public function index(){
        $posts = Post::with('user')->latest()->paginate(10);

        return response()->json($posts);
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type'=>'required|in:news,update,task',
        ]);
        $post = Post::create([
            'title'=> $request->title,
            'message'=> $request->message,
            'type'=> $request->type,
            'user_id' =>Auth::id(),
        ]);
        return response()->json(['message' => 'Post created successfully!', 'post'=> $post], 201);
    }
    public function show(Post $post){
        return response()->json($post->load('user'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:news,update,task',
        ]);

        if (Auth::id() !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403); // 403 Forbidden
        }

        $post->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
        ]);

        return response()->json(['message' => 'Post updated successfully!', 'post' => $post]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403); // 403 Forbidden
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully!']);
    }
}
