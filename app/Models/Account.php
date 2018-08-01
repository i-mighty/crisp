<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //
	protected $fillable = ['token', 'balance'];

	public  function owner(){
		return $this->morphTo();
	}
	public function recieved() {
		return $this->hasMany('App\Models\Payment', 'receiver_id');
	}
	public function sent(){
		return $this->hasMany('App\Models\Payment', 'sender_id');
	}
	public function getUpdatedAtAttribute($value) {
		return str_replace(" ", "T", $value);
	}
	public function getCreatedAtAttribute($value) {
		return str_replace(" ", "T", $value);
	}
	public function cards(){
		return $this->hasMany('App\Models\Card');
	}
	public function createCard($card){
		return $this->cards()->create($card);
	}
	public function updateCard($card, $id){
		$current = $this->account->cards()->where('id', $id);
		$current = $card;
		$this->account->card()->save($current);
	}
	public function send($cost){
	    $this->balance-=$cost;
	    $this->save();
    }
    public function receive($cost){
	    $this->balance+=$cost;
	    $this->save();
    }
    public function recharge($value){
	    $this->balance+=$value;
	    $this->save();
    }
}
