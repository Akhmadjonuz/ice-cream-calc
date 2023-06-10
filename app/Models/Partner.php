<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $table = 'partners';

    /**
     * Get the exchanges for the partner.
     */

    public function exchanges()
    {
        return $this->hasMany(Exchange::class);
    }

    /**
     * Get the debts for the partner.
     */

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }
}
