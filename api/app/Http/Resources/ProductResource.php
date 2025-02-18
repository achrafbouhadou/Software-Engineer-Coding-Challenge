<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->getImageUrl(),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }

    protected function getImageUrl() // Normaly we move this to helper file
    {
        if ($this->isValidUrl($this->image)) {
            return $this->image;
        }
        if ($this->image) {
            return asset($this->image);
        }

        return null;
    }

    protected function isValidUrl($string): bool
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== false;
    }
}
