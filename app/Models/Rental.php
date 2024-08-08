<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'rental_date',
        'return_date',
        'price_per_day',
        'total_price',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }
}
