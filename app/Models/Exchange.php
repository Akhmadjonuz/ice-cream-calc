<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    protected $table = 'exchanges';

    // Has Many Relations
    public function products()
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function partners()
    {
        return $this->hasMany(Partner::class, 'id', 'partner_id');
    }
}
