<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    // relations

    public function Nbu()
    {
        return $this->hasMany(Nbu::class, 'product_id', 'id')->latest();
    }
}