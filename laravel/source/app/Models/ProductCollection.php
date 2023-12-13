<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ProductCollection extends Eloquent
{
    use HasFactory;
    protected $table = 'productcollection';
    protected $primaryKey = ['productId', 'collectionId'];
    public $incrementing = false;
    protected $fillable = ['deleted'];
}
