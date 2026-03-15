<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResponse extends JsonResource
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
            'name' => $this->description,
            'description' => $this->description,
            'amount' => $this->amount,
            'type' => $this->type,
            'category' => $this->category,
            'expense_date' => $this->expense_date,
            'image' => $this->image,
            'user_id' => $this->user_id,
            'actions' =>'action'
        ];
    }
}
