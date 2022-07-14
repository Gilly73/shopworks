<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'week_commence_date'
    ];

    public function rotaWithShifts($shopId,$dateCommencing)
    {
        return $this->where('shop_id',$shopId)->where('week_commence_date',$dateCommencing)->with('shifts');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function shop()
    {
        //did not use has (test failed) had to use belongsTo
        return $this->belongsTo(Shop::class);
    }
}
