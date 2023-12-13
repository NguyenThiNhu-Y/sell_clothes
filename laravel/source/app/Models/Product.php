<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Product extends Eloquent
{
    use HasFactory;
    protected $table = 'product';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'price', 'description', 'img', 'categoryId', 'deleted'];

    public function Variants()
    {
        return $this->hasMany(Variation::class, 'productId', 'id');
    }
}
