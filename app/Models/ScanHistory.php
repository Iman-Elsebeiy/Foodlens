<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanHistory extends Model
{
    protected $table="scan_histories";
    protected $fillable = [
         'total_food_items', 'calories_consumed', 'image', 'user_id', 'food_id', 'daily_data_id',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id', 'id');
    }
}
