<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ProductSale extends Eloquent
{
    use HasFactory;
    protected $table = 'productsales';
    protected $primaryKey = ['productid', 'saleid'];
    public $incrementing = false;
    protected $fillable = ['deleted'];
}
