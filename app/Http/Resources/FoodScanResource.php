<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodScanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            "total_calories" => $this['total_calories'],
            'image_path' => $this['image_path'],
            'food_items' => $this['food_items'],
            'quantity' => $this['total_food_items']
        ];
    }
}
