<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\Post\PostCreate;
use App\Services\Post\PostUpdate;
use App\Http\Resources\PostResource;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\SearchPostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    public function search(SearchPostRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $slug = collect($request->validated())->get('slug');
        $posts = Post::whereHas('postSubCategory', fn ($query) => $query->where('slug', $slug))->latest()->paginate(12);

        return PostResource::collection($posts);
    }

    public function slugs(): \Illuminate\Http\JsonResponse
    {
        $posts = Post::with(['postCategory', 'postSubCategory'])->get();

        $postSlugs = $posts->map(function ($post) {
            $categorySlug = $post->postCategory->slug;
            $subCategorySlug = $post->postSubCategory->slug;
            $postSlug = $post->slug;

            return "/$categorySlug/$subCategorySlug/$postSlug";
        });

        return response()->json([
            'data' => $postSlugs
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return PostResource::collection(Post::with(['postCategory', 'postSubCategory'])->latest()->paginate(6));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StorePostRequest  $service
     * @param  App\Services\Post\PostCreate  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request, PostCreate $service): PostResource
    {
        $valid = collect($request->validated());

        return new PostResource($service->createPost($valid), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post, PostUpdate $service): PostResource
    {
        $valid = collect($request->validated());

        return new PostResource($service->updatePost($valid, $post), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        //
    }
}
