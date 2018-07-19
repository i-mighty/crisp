<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Card;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;

class RechargeController extends Controller
{
//	use Paystack;
    //
	/**
	 * Redirect the User to Paystack Payment Page
	 * @return bool
     */
	//Paystack
//	public function redirectToGateway() {
//		return Paystack::getAuthorizationUrl()->redirectNow();
//	}
//
//	public function makePost(){
//		return redirect('pay')->content('');
//	}
//	//Flutter wave
    function card($request){
        if(Card::find($request->card_id) == null){
            $card = Card::create([
                'cardno' => $request->cardno,
                'cvv' => $request->cvv,
                'expirymonth' => $request->expirymonth,
                'expiryyear' => $request->expiryyear,
                'token' => null
            ]);
            return true;
        }
        else{
            $card = Card::find($request->card_id);
            if($card == null){

            }
        }
    }
	function getKey($seckey){
		$hashedkey = md5($seckey);
		$hashedkeylast12 = substr($hashedkey, -12);

		$seckeyadjusted = str_replace("FLWSECK-", "", $seckey);
		$seckeyadjustedfirst12 = substr($seckeyadjusted, 0, 12);

		$encryptionkey = $seckeyadjustedfirst12.$hashedkeylast12;
		return $encryptionkey;

	}
	function encrypt3Des($data){
		$encData = openssl_encrypt($data, 'DES-EDE3', $this->getKey(getenv('RAVE_TEST_SECRET_KEY')), OPENSSL_RAW_DATA);
		return base64_encode($encData);
	}
	public function collectCard($card, $pin, $amount){
//	    $card  = $this->card($request);
		$parameters = [
			'PBFPubKey' => getenv('RAVE_TEST_PUBLIC_KEY'), 'amount' => $amount, 'pin' => $pin, 'suggested_auth'=>'PIN', 'phonenumber' => '09032435423', 'email' => 'josadegboye@gmail.com', 'IP' => '355426087298442',
			'txRef' => (getenv('PAY_REF_PREFIX').Carbon::now()->toDateTimeString()),
			];
			return collect($card)->merge($parameters);
	}
	public function encryptCard($card, $pin, $amount){
		return  $this->encrypt3Des(collect($this->collectCard($card, $pin, $amount)));
	}
	public function initPayment($card, $pin, $amount){
		$payload = $this->encryptCard($card, $pin, $amount);
		$data = ['PBFPubKey' => getenv('RAVE_TEST_PUBLIC_KEY'), 'client' => $payload, 'alg' =>"3DES-24"];
		$client = new Client();
		$promise = $client->requestAsync('POST', getenv('RAVE_CHARGE_SANDBOX'), [
            'json' => $data
        ]);
		$promise->then(
			function (ResponseInterface $response){
				return $response;
			},
			function (BadResponseException $e){
			    return $e;
//				$this->sendError();
			}
		);
		return $promise;
	}
	public function getOtp(){
		$message = [
			'status' => 'success', 'message' => 'RECEIVE_OTP', 'user' => User::find(auth()->user()->id)->with('account.card')
		];
		return response(collect($message), 00);
	}
	public function sendError(){
		$message = [
			'status' => 'failed', 'message' => 'CHARGE_ERROR', 'user' => User::find(auth()->user()->id)->with('account.card')
		];
		return response(collect($message), 00);
	}
	public function validatePayment($otp){

	}
}
