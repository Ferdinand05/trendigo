<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'limit_name' => Str::limit($this->name, 45, '...'),
            'description' => $this->description,
            'category' => $this->category->name,
            'slug' => $this->slug,
            'price' => intval($this->price),
            'weight' => intval($this->weight),
            'stock' => intval($this->stock),
            'is_active' => $this->is_active == 1 ? true : false,
            'created_at' => $this->created_at->diffForHumans(),
            'product_images' => $this->images
        ];
    }
}
