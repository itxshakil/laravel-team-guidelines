<?php
namespace App\Http\Controllers;
use App\Models\Post;
class SampleController {
    public function index() {
        $posts = Post::with('author')->latest()->get();
        return view('posts.index', compact('posts'));
    }
}
