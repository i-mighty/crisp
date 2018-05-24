<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Account::class, function (Faker $faker) {
    return [
		'number' => str_random(16),
	    'user_id' => factory(\App\User::class)->create()->id,
	    'token' => str_random('16')
    ];
});
