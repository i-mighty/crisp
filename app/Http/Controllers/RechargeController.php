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
	 * @return Url
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
	function encrypt3Des($data, $key){
		$encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
		return base64_encode($encData);
	}
	public function collectCard($request, $pin, $amount){
	    $card  = $this->card($request);
		$parameters = [
			'PBFPubKey' => getenv('RAVE_PUBLIC_KEY'), 'amount' => $amount, 'email' => $card->account()->owner()->email, 'IP' => request()->getClientIp(),
			'txRef' => (getenv('PAY_REF').Carbon::now()),
			];
			return collect($card)->put($parameters);
	}
	public function encryptCard($card, $pin, $amount){
		return  $this->encrypt3Des(collect($this->collectCard($card, $pin, $amount)), $this->getKey(getenv('RAVE_SECRET_KEY')));
	}
	public function initPayment($card, $pin, $amount){
		$payload = $this->encryptCard($card, $pin, $amount);
		$data = collect(['PBFPubKey' => getenv('RAVE_PUBLIC_KEY'), 'client' => $payload, 'alg' =>"3DES-24"]);
		$client = new Client();
		$promise = $client->postAsync(getenv('RAVE_CHARGE_SANDBOX'), $data);//Async Call
		$promise->then(
			function (ResponseInterface $response){
				$response->getBody()->
				$this->getOtp();//Proceed
			},
			function (BadResponseException $e){
				$this->sendError();
			}
		);
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
