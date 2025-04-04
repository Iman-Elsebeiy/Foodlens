<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyData extends Model
{
    protected $table="daily_data";
    protected $fillable = [
        'calories_consumed',
        'user_id',
        'water',
        'weight',
        'sleep',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
