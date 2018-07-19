<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    //
	protected $fillable = [
		'cardno', 'cvv', 'expirymonth', 'expiryyear', 'token'
	];
	public function saveToken($token){
		$this->token = $token;
		$this->save();
	}
	public function account(){
		return $this->belongsTo('App\Models\Account');
	}
}
