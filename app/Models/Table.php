<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['name', 'status', 'qr_token'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
