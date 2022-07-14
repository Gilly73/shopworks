<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active'
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1)->get();
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function rota()
    {
        return $this->hasMany(Rota::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }



}
