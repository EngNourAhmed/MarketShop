<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResponse extends JsonResource
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
            'vendor' => $this->supplier?->name ?? $this->customer?->name,
            'type' => $this->type,
            'amount' => $this->amount,
            'date' => $this->due_date,
        ];
    }
}
