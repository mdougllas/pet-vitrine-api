<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostSubCategoryRequest;
use App\Http\Requests\UpdatePostSubCategoryRequest;
use App\Models\PostSubCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class PostSubCategoryController extends Controller
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
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostSubCategoryRequest $request): RedirectResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PostSubCategory $postSubCategory): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostSubCategory $postSubCategory): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostSubCategoryRequest $request, PostSubCategory $postSubCategory): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostSubCategory $postSubCategory): RedirectResponse
    {
        //
    }
}
