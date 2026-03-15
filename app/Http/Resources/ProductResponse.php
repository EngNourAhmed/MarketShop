<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'image' => $this->image,
            'code' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'factory' =>'factory',
        ];
    }
}
