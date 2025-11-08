<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'barcode',
        'harga_modal',
        'harga_jual',
        'image',
        'category_id',
        'subcategory_id', 
    ];

    // Relasi ke kategori utama
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke subkategori
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    // Atribut tambahan: margin
    public function getMarginAttribute()
    {
        return $this->harga_jual - $this->harga_modal;
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Get the full URL for the product image
     * 
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // Check if image exists in storage
        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        return null;
    }

    /**
     * Check if product has a valid image
     * 
     * @return bool
     */
    public function hasImage()
    {
        return $this->image && Storage::disk('public')->exists($this->image);
    }
}