<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Str;
use App\Models\PostCategory;
use App\Models\PostSubCategory;
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
        $title = $request->get('title');
        $category = PostCategory::where('title', $request->get('category'))->first();
        $subCategory = PostSubCategory::where('title', $request->get('sub_category'))->first();

        $this->post->body = $request->get('body');
        $this->post->image_url = $request->get('image_url');
        $this->post->postCategory()->associate($category);
        $this->post->postSubCategory()->associate($subCategory);
        $this->post->slug = Str::kebab($request->get('title'));
        $this->post->title = $title;
        $this->post->user()->associate(Auth::user());

        $this->post->save();

        return $this->post;
    }
}
