<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['order_id', 'subtotal', 'tax', 'total', 'payment_method', 'status', 'cashier_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
