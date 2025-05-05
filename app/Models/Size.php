<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $appends = ['display_name'];

    public function getDisplayNameAttribute()
    {
        return 'EU ' . $this->name;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_sizes')
            ->withPivot('quantity')
            ->withTimestamps();
    }
} 