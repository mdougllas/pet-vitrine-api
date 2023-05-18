<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Str;
use App\Models\PostCategory;
use App\Models\PostSubCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PostUpdate
{
    /**
     * Instance of Post model.
     *
     * @var \App\Models\Post
     */
    private $post;

    /**
     * Instantiate the class.
     *
     * @return void
     */
    public function __construct()
    {
        $this->post = new Post;
    }

    /**
     * Edit and persist post from database.
     *
     * @param Collection $request
     * @return Post
     */
    public function updatePost(Collection $request, $post): Post
    {
        $this->post = $post;

        $this->post->body = $request->get('body');
        $this->post->image_alt_text = $request->get('image_alt_text');
        $this->post->image_url = $request->get('image_url');
        $this->post->slug = Str::kebab($request->get('title'));
        $this->post->title = $request->get('title');

        $this->updatePostRelationships($request);

        $this->post->save();

        return $this->post;
    }

    /**
     * Update the relationships for the post
     *
     * @param Collection $request
     * @return void
     */
    private function updatePostRelationships(Collection $request): void
    {
        $category = PostCategory::where('title', $request->get('category'))->first();
        $subCategory = PostSubCategory::where('title', $request->get('sub_category'))->first();

        $this->post->postCategory()->disassociate();
        $this->post->postSubCategory()->disassociate();

        $this->post->postCategory()->associate($category);
        $this->post->postSubCategory()->associate($subCategory);
    }
}
