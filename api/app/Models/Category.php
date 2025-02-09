<?php

namespace App\Models;

<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> c2c58b7 (Feat : seed a large data)
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
<<<<<<< HEAD
=======
    use HasFactory;

>>>>>>> c2c58b7 (Feat : seed a large data)
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

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

}
