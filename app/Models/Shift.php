<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'rota_id',
        'staff_id',
        'start_time',
        'end_time',
    ];

    public function rota()
    {
        return $this->belongsTo(Rota::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

}
