<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'user' => $this->user()->select(['name', 'is_admin', 'is_agent', 'phone'])->first(),
            'description' => $this->description,
            'street' => $this->street,
            'township' => $this->township,
            'city' => $this->city,
            'state_or_division' => $this->state_or_division,
            'price' => $this->price,
            'width' => $this->width,
            'length' => $this->length,
            'status' => ucfirst($this->status), // need to remove that ucf() function
            'view_count' => $this->view_count,
            'is_declined' => $this->is_declined,
            'created_at' => $this->created_at,
            'photos' => PhotoResource::collection($this->photos),
            'boost' => $this->boosts()->where('end', '>', now()->format('Y-m-d H:i:s'))->first()
        ];
    }
}
