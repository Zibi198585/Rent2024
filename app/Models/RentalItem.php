<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class RentalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'item_name',
        'quantity',
        'price_per_day',
        'total_price',

    ];

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    // Dodaj mutator, aby automatycznie obliczać total_price
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->calculateTotalPrice();
    }

    public function setPricePerDayAttribute($value)
    {
        $this->attributes['price_per_day'] = $value;
        $this->calculateTotalPrice();
    }

    protected function calculateTotalPrice()
    {
        if ($this->quantity && $this->price_per_day) {
            $this->attributes['total_price'] = $this->quantity * $this->price_per_day;
        }
    }

}
