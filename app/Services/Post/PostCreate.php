<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Str;
use App\Models\PostCategory;
use App\Models\PostSubCategory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PostCreate
{
    /**
     * Instance of Post model.
     *
     * @var \App\Models\Post
     */
    private $post;

    public function __construct()
    {
        $this->post = new Post;
    }

    public function createPost(Collection $request): Post
    {
        $post = new Post;

        $title = $request->get('title');
        $category = PostCategory::where('title', $request->get('category'))->first();
        $subCategory = PostSubCategory::where('title', $request->get('sub_category'))->first();

        $post->body = $request->get('body');
        $post->image_url = $request->get('image_url');
        $post->postCategory()->associate($category);
        $post->postSubCategory()->associate($subCategory);
        $post->slug = Str::kebab($request->get('title'));
        $post->title = $title;
        $post->user()->associate(Auth::user());

        $post->save();

        return $post;
    }
}
