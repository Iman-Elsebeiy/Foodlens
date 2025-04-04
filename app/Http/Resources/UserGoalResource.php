<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGoalResource extends JsonResource
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
            'user_id' => $this->user_id,
            'target_calories' => $this->target_calories,
            'target_water' => $this->target_water,
            'target_sleep' => $this->target_sleep,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
