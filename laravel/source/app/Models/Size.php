<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Size extends Eloquent
{
    use HasFactory;
    protected $table = 'size';
    protected $primaryKey = ['variantId', 'size'];
    public $incrementing = false;
    protected $fillable = ['quantity', 'deleted'];
}
