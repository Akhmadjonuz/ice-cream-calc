<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsInput extends Model
{
    use HasFactory;

    protected $table = 'products_input';

    public function products()
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }


    public function nbu()
    {
        return $this->hasMany(Nbu::class, 'id', 'nbu_id');
    }
}
