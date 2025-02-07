<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['id' , 'name', 'parent_id'];

    public $incrementing = false; 

    protected $casts = [
        'id' => 'string',
        'parent_id' => 'string',
    ];


    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function child()
    {
        return $this->hasOne(Category::class, 'parent_id');
    }

}
