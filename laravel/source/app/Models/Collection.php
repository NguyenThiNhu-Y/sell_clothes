<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Collection extends Eloquent
{
    use HasFactory;
    protected $table = 'collection';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'visible', 'mobileBanner', 'pcBanner', 'deleted'];
}
