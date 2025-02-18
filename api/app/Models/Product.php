<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['id', 'name', 'description', 'price', 'image'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $with = ['categories'];

    protected $casts = [
        'id' => 'string',
        'price' => 'float',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
