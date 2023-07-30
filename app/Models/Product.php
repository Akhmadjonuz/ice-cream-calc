<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    public function caterogies()
    {
        return $this->hasMany(Caterogy::class, 'id', 'caterogy_id');
    }

    public function settings()
    {
        return $this->hasMany(Setting::class, 'id', 'type_id');
    }
}