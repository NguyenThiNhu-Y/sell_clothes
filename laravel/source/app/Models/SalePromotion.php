<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SalePromotion extends Eloquent
{
    use HasFactory;
    protected $table = 'salespromotion';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'discount', 'timeStart', 'timeEnd', 'visible', 'mobileBanner', 'pcBanner', 'deleted'];
}
