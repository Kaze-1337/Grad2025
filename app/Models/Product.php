<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'SKU',
        'stock_status',
        'featured',
        'has_size',
        'image',
        'images',
        'category_id',
        'brand_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_sizes')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function imports()
    {
        return $this->hasMany(ProductImport::class);
    }

    public function getTotalQuantityAttribute()
    {
        if (!$this->has_size) {
            return 0;
        }
        return $this->sizes->sum('pivot.quantity');
    }

    public function getStockStatusAttribute($value)
    {
        if (!$this->has_size) {
            return $value;
        }
        return $this->total_quantity > 0 ? 'instock' : 'outofstock';
    }
}
