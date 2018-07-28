<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Card;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
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
        try {
            $promise = $client->request('POST', getenv('RAVE_CHARGE_SANDBOX'), [
                'json' => $data
            ]);
            $result = json_decode($promise->getBody()->getContents());
            switch ($result->data->authModelUsed){
                case "VBVSECURECODE":$response = $this->returnAuthUrl($result);
                case "PIN": $response = $this->getOtp($result);
            }
            return $response;
        } catch (GuzzleException $e) {
            return $this->sendError($result);
        }
	}
	public function getOtp($res){
        // Save transaction pending otp verification
        $tx = Transaction::create([
            'flwRef' => $res->data->flwRef,
            'txRef' => $res->data-txRef,
        ]);
		$message = [
			'status' => 'success', 'message' => 'authModel', 'value'=>'PIN', 'tx' => $tx
		];
		return response(collect($message), 200);
	}
	public function sendError($res){
		$message = [
			'status' => 'failed', 'message' => 'CHARGE_ERROR',
		];
		return response(collect($message), 400);
	}
	public function validatePayment($otp){
        
	}
	public function returnAuthUrl($res){
        //Save transaction pending validation
        $tx = Transaction::create([
            'flwRef' => $res->data->flwRef,
            'txRef' => $res->data->txRef
        ]);
        return response(collect([
            'status' => 'success', 'message' => 'authModel',
            'value' => 'VBVSECURECODE', 'auth_url' => $res->data->authModelUsed,
            'tx' => $tx]), 200);
    }

    public function otpCallback(){

    }
}
