<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Post;

/*
|--------------------------------------------------------------------------
| FEED (READ)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $posts = Post::latest()->get();
    return view('feed', compact('posts'));
});

/*
|--------------------------------------------------------------------------
| CREATE
|--------------------------------------------------------------------------
*/
Route::post('/posts', function (Request $request) {
    $request->validate([
        'content' => 'required',
    ]);

    Post::create([
        'content' => $request->content,
    ]);

    return redirect('/');
});

/*
|--------------------------------------------------------------------------
| EDIT (SHOW FORM)
|--------------------------------------------------------------------------
*/
Route::get('/posts/{post}/edit', function (Post $post) {
    return view('edit', compact('post'));
});

/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/
Route::put('/posts/{post}', function (Request $request, Post $post) {
    $request->validate([
        'content' => 'required',
    ]);

    $post->update([
        'content' => $request->content,
    ]);

    return redirect('/');
});

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/
Route::delete('/posts/{post}', function (Post $post) {
    $post->delete();
    return redirect('/');
});
