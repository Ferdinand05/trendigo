<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
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
            'user_id' => $this->user_id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'limit_name' => Str::limit($this->product->name, 30, '...'),
                'stock' => $this->product->stock,
                'category' => $this->product->category->name,
                'weight' => $this->product->weight,
                'price' => intval($this->product->price),
                'slug' => $this->product->slug,
                'description' => $this->product->description,
                'is_active' => $this->is_active == 1 ? true : false,
                'product_images' => $this->product->images
            ],
            'quantity' => $this->quantity,
            'subtotal' => intval($this->subtotal),
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
