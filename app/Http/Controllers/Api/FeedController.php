<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    // GET /api/posts
    public function index()
    {
        return response()->json(
            Post::latest()->get()
        );
    }

    // POST /api/posts
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post = Post::create([
            'content' => $request->content,
        ]);

        return response()->json($post, 201);
    }

    // GET /api/posts/{id}
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return response()->json($post);
    }

    // PUT /api/posts/{id}
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post = Post::findOrFail($id);
        $post->update([
            'content' => $request->content,
        ]);

        return response()->json($post);
    }

    // DELETE /api/posts/{id}
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}
