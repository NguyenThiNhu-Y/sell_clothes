<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class DetailReceipt extends Eloquent
{
    use HasFactory;
    protected $table = 'detailreceipt';
    protected $primaryKey = ['receiptId', 'variantId'];
    public $incrementing = false;
    protected $fillable = ['price', 'quantity', 'deleted'];
}
