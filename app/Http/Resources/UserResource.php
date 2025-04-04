<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'image'          => $this->image ? asset("storage/".$this->image) : null,
            'height'          => $this->height??null,
            'weight'          => $this->weight??null,
            'age'             =>   $this->birth_date?(int)abs(now()->diffInYears(Carbon::parse($this->birth_date))):null,
            "birth_date"      => $this->birth_date??null,
            "phone" => $this->phone??null,

        ];
    }
}
