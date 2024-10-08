<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'birthday' => $this->birthday,
            'is_admin' => $this->is_admin,
            'is_agent' => $this->is_agent,
            'status' => $this->status,
            'phone' => $this->phone,
            'address' => $this->address,
            'photo' => $this->photo ? '/' . $this->photo : null,
            'created_at' => $this->created_at
        ];

        if ($this->token) $data['token'] = $this->token;

        return $data;
    }
}
