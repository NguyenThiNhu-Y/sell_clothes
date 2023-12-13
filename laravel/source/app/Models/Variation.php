<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Variation extends Eloquent
{
    use HasFactory;
    protected $table = 'variation';
    protected $primaryKey = 'id';
    protected $fillable = ['productId', 'colorId', 'thumbnail', 'deleted'];

    public function Sizes()
    {
        return $this->hasMany(Size::class, 'variantId', 'id');
    }

    public function Images()
    {
        return $this->hasMany(Image::class, 'variantId', 'id');
    }
}
