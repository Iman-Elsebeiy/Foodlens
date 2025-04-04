<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllFoodScans extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'image' =>$this->image,
            'food_name'=> $this->food->food_name,
            'calories_consumed'=>$this->calories_consumed,
            'total_food_items'=>$this->total_food_items,
            'user_id'=>$this->user_id,
        ];
    }
}
