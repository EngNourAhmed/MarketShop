<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            
            'customer_name' => $this->customer?->name,
            'code' => $this->order_code,
            'total' => $this->total,
            'date' => $this->created_at,
            'status' => $this->status,
            'total' => $this->total
        ];
    }
}
