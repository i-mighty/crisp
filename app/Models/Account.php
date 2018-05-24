<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //
	protected $fillable = ['number', 'user_id', 'token', 'balance'];

	protected  function user(){
		return $this->belongsTo('App\User');
	}
	protected function recieved() {
		return $this->hasMany('App\Models\Account', 'receiver_id');
	}
	protected function sent(){
		return $this->hasMany('App\Models\Payment', 'sender_id');
	}
}
