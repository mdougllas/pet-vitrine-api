<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use App\Services\Post\PostCreate;
use App\Http\Resources\PostResource;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): PostResource
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StorePostRequest  $request
     * @param  App\Services\Post\PostCreate  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request, PostCreate $post): PostResource
    {
        $valid = collect($request->validated());;

        return new PostResource($post->createPost($valid), 201);
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
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        //
    }
}
