<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['order_id', 'money', 'vnp_response_code', 'code_vnpay', 'code_bank', 'time'];

    protected $table = "payment";
}
