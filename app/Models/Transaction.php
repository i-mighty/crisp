<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model{
    protected $fillable = [
        'flwRef', 'otp', 'txRef', 'completed','payload'
    ];
    protected $primaryKey = 'txRef';
    public $incrementing = false;
}
