<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $orderCode = $this->order?->order_code ?? $this->sale?->invoice_number;
        $vendorName = $this->supplier?->name ?? $this->sale?->customer?->name;

        return [
            'order_code' => $orderCode,
            'order_amount' => $this->order_amount,
            'date' => $this->date,
            'vendor_name' => $vendorName,
            'commission' => $this->commission,
        ];
    }
}
