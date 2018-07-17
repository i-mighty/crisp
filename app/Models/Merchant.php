<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Merchant extends Model
{
	use Notifiable;

	protected $fillable = [
		'username', 'email', 'password', 'token', 'uid'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];
	public function account(){
		return $this->morphOne('App\Models\Account', 'owner');
	}

	public function generateToken()
	{
		$this->api_token = str_random(60);
		$this->save();

		return $this->api_token;
	}
	public function tempToken(){
		$this->token = str_random(60);
	}
}

