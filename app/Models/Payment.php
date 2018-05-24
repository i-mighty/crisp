<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
	protected $fillable = ['sender_id', 'sender_id', 'receiver_id', 'receiver_id', 'amount', 'token'];

	protected function sender () {
		$this->belongsTo('App\Models\Account', 'sender_id');
	}
	protected function receiver(){
		$this->belongsTo('App\Models\Account', 'receiver_id');
	}
}
