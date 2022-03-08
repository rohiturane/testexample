<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Post;

class PostController extends Controller
{
    public function userPostCount(Request $request)
    {
        $userPost = [];
        $date = Carbon::now()->subDays(7);
        $posts = Post::join('users','users.id','=','posts.user_id')
            ->selectRaw('count(posts.id) as post_count,users.name, users.id')->groupBy('user_id')->having('post_count','>',10)
            ->where('posts.created_at', '>=', $date)->get();
        foreach ($posts as $value) {
            $temp =[];
            $lastPost = Post::where('user_id',$value->id)->latest()->first();
            $temp['username']=$value->name;
            $temp['total_posts_count']=$value->post_count;
            $temp['last_post_title']=$lastPost->title;
            $userPost[]=$temp;
        }
        $userPosts = $this->getPaginator($request, $userPost);
        return view('articleUser',compact('userPosts'));
    }

    private function getPaginator(Request $request, $items)
    {
        $total = count($items); 
        $page = $request->page ?? 1; 
        $perPage = 3;
        $offset = ($page - 1) * $perPage;
        $items = array_slice($items, $offset, $perPage);

        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query()
        ]);
    }

    public function create()
    {
        $users = User::get();
        return view('create',compact('users'));
    }    

    public function save(Request $request)
    {
        $input_array = $request->all();
        Post::create($input_array);
        return redirect('/');
   }
}
