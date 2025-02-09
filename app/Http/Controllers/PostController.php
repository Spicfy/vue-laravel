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
        $posts = Post::with('user')->get();

        return response()->json($posts, 200);
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type'=>'required|in:news,update,task',
        ]);

        $user = Auth::user();



        $post = Post::create([
            'title'=> $request->title,
            'message'=> $request->message,
            'type'=> $request->type,
            'user_id' => $user->id,
            'firstName'=> $user->first_name,
            'lastName'=> $user->last_name,
        ]);
        return response()->json(['message' => 'Post created successfully!', 'post'=> $post], 201);
    }
    public function show($id){
        $post = Post::with('user')->findOrFail($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        if($request->user()->id !== $post->user_id){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
       $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:news,update,task',
        ]);



        if (Auth::id() !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403); // 403 Forbidden
        }

        $post->update($data);

        return response()->json(['message' => 'Post updated successfully!', 'post' => $post]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        if (Auth::id() !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403); // 403 Forbidden
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully!']);
    }
    public function getAllPosts(){
        $posts = Post::with('user')->get();

        return response()->json($posts);
    }
}
