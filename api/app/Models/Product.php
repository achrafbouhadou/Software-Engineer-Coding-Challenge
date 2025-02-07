<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = ['id', 'name', 'description', 'price', 'image'];
    public $incrementing = false; 
    protected $keyType = 'string'; 

    protected $casts = [
        'id' => 'string',
        'price' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
