<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Receipt extends Eloquent
{
    use HasFactory;
    protected $table = 'receipt';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'timeOrder', 'paymentTime', 'paymentMethod'];
}
