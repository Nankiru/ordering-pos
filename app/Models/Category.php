<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'image'];

    public function items()
    {
        return $this->hasMany(\App\Models\Item::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;
        return asset('uploads/category/' . $this->image);
    }
}
