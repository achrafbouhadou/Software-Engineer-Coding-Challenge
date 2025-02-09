<?php

namespace App\Models;

<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> c2c58b7 (Feat : seed a large data)
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
<<<<<<< HEAD
=======
    use HasFactory;

>>>>>>> c2c58b7 (Feat : seed a large data)
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
