<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PostCategoryResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'category' => new PostCategoryResource($this->postCategory),
            'created_at' => $this->created_at,
            'cta' => $this->cta,
            'cta_link' => $this->cta_link,
            'image_alt_text' => $this->image_alt_text,
            'image_url' => $this->image_url,
            'sub_category' => new PostSubCategoryResource($this->postSubCategory),
            'slug' => $this->slug,
            'title' => $this->title,
            'updated_at' => $this->updated_at,
        ];
    }
}
