<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'description', 'price', 'category_id', 'image'];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;
        return asset('uploads/items/' . $this->image);
    }
}
