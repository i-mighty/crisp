<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model{
    protected $fillable = [
        'flwRef', 'otp', 'txRef', 'completed','payload'
    ];
    public function card(){
        return $this->belongsTo('App\Card');
    }
    protected $primaryKey = 'txRef';
    public $incrementing = false;
}
