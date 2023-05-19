<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use Illuminate\Http\Response;
use App\Http\Resources\PostResource;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\RelatedPostsRequest;
use App\Http\Resources\PostCategoryResource;
use App\Http\Requests\StorePostCategoryRequest;
use App\Http\Requests\UpdatePostCategoryRequest;

class PostCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StorePostCategoryRequest $request): PostCategoryResource
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostCategoryRequest $request): RedirectResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PostCategory $postCategory): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostCategory $postCategory): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostCategoryRequest $request, PostCategory $postCategory): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostCategory $postCategory): RedirectResponse
    {
        //
    }

    public function relatedPosts(RelatedPostsRequest $request, PostCategory $category)
    {
        $valid = $request->validated();
        $subCategories = $category->postSubCategories()->get();

        $articles = $subCategories->map(function ($subCategory) use ($valid) {
            return $subCategory->posts()->where('slug', '!=', $valid['slug'])->first();
        });

        return PostResource::collection($articles);
    }
}
