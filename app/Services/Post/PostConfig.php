<?php

namespace App\Services\Post;

class PostConfig
{
    /**
     * Specify the rules for request validation for posts.
     *
     * @return array
     */
    public static function validationRules()
    {
        return [
            'body' => ['required', 'string'],
            'category' => ['required', 'exists:App\Models\PostCategory,title'],
            'sub_category' => ['required', 'exists:App\Models\PostSubCategory,title'],
            'title' => ['required', 'string'],
            'image_alt_text' => ['required', 'string'],
            'image_url' => ['required', 'url'],
        ];
    }
}
