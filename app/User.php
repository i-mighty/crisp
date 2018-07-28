<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Webpatser\Uuid\Uuid;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'email', 'password', 'uid', 'avatar'
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
		$this->api_token = Uuid::generate()->string;
		$this->save();

		return $this->api_token;
	}
	public function createAccount(){
    	$this->account()->create(['balance' => 2000]);
	}
	public function getUpdatedAtAttribute($value) {
		return str_replace(" ", "T", $value);
	}
	public function getCreatedAtAttribute($value) {
		return str_replace(" ", "T", $value);
	}
}
