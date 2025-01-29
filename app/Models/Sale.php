<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['time_of_sale', 'product_id', 'price', 'quantity', 'product_name'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            $product = Product::find($sale->product_id);
            if($product) {
                $sale->price = $product->price;
                $sale->product_name = $product->name;
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
