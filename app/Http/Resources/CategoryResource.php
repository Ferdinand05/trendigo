<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description_limit' => Str::limit($this->description, 70, '...'),
            'description' => $this->description,
            'products_count' => $this->products->count(),
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
