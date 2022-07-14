<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'surname',
        'shop_id'
    ];


    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
