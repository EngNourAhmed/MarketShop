<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResponse extends JsonResource
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
            'card_code' => $this->card_number,
            'name' => $this->card_holder,
            'type' => $this->type,
            'points' => $this->points,
            'value' => $this->balance,
            'distribution' => $this->distribution ?? '',
            'status' => $this->status ?: 'active',
            'actions' => 'action',


        ];
    }
}
