<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Payment;
use App\User;
use Illuminate\Http\Request;
use \Webpatser\Uuid\Uuid;

class PurchaseController extends Controller
{
    //
//    public function __construct(){
//        $this->middleware("auth:api");
//    }

    public function makePurchase(Request $request, $receiver){
        $price = $request->price;
		$user = auth('api')->user();
		$merchant = User::find($receiver);
		$payment = $user->account->sent()->create([
			'receiver_id' => $receiver,
			'amount' => $price,
			'token' => Uuid::generate()->string,
			'sent' => true,
            'received' => false //Add default value later
		]);
		$user->account->send($price);
//		$merchant->account->received()->add($payment);
		$payment->received = true;
		$payment->save();
		$merchant->account->receive($price);
		return response(['status' => '200', 'message' =>'transaction success',
                        'sender_account' => collect($user->account),
                        'transaction' => collect($payment),
                        'receiver_account'=> collect($merchant->account)]);
	}
}
