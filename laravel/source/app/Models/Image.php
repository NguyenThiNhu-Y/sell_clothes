<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Image extends Eloquent
{
    use HasFactory;
    protected $table = 'image';
    protected $primaryKey = 'id';
    protected $fillable = ['url', 'variantId', 'deleted'];
}
