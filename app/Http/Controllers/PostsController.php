<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Post;


class PostsController extends Controller
{
     public function index()
    {
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            $posts = $user->feed_posts()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'posts' => $posts,
            ];
        }

        // Welcomeビューでそれらを表示
        return view('welcome', $data);
    }
    
    public function store (Request $request) 
    {
        $this->validate($request, [
           'content' => 'required|max:191', 
            'image' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $postimage = $request->image;
        $path = Storage::disk('s3')->putFile('/postimages', $postimage, 'public');
        $request->user()->posts()->create([
            'content' => $request->content, 
            'image' => $request->image = $path,
        ]);
        
        return redirect('/');
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (\Auth::id() === $post->user_id) {
            Storage::disk('s3')->delete($post->image);
            $post->delete();
        }

        return back();
    }
}