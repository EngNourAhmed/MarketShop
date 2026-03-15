<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawResponse extends JsonResource
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
            'customer' => $this->customer?->name,
            'points' => $this->points,
            'amount' => $this->amount,
            'type' => $this->type?:'type',
            'transfer_info' => $this->reference,
            'actions' => ''
        ];
    }
}
