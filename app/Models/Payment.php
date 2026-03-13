<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
    'full_name',
    'email',
    'amount',
    'user_id',
    'order_id',
    'invoice_id',
    'status',
];

}
