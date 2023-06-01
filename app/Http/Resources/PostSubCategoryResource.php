<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSubCategoryResource extends JsonResource
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
            'category' => new PostCategoryResource($this->postCategory),
            'description' => $this->description,
            'title' => $this->title,
            'slug' => $this->slug,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }
}
