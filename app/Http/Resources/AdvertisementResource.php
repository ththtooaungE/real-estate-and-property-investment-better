<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvertisementResource extends JsonResource
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
            'title' => $this->title,
            'owner' => $this->owner,
            'photo' => '/' . $this->photo,
            'start' => $this->start,
            'end' => $this->end,
            'created_at' => $this->created_at
        ];
    }
}
