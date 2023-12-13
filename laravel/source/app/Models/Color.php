<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Color extends Eloquent
{
    use HasFactory;
    protected $table = 'color';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'deleted'];
}
