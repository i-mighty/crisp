<?php

namespace Tests\Feature;

use App\Models\Card;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\RechargeController;

class RechargeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    private $controller;
    private $card;
    private $card_string;
    private $card_encrypt;

    protected function setUp(){
        $this->controller = new RechargeController();
        $this->card_string = '{
                  "PBFPubKey": "FLWPUBK-4e581ebf8372cd691203b27227e2e3b8-X",
                  "cardno": "5438898014560229",
                  "cvv": "890",
                  "expirymonth": "09",
                  "expiryyear": "19",
                  "currency": "NGN",
                  "country": "NG",
                  "amount": "10",
                  "email": "user@gmail.com",
                  "phonenumber": "0902620185",
                  "firstname": "temi",
                  "lastname": "desola",
                  "IP": "355426087298442",
                  "txRef": "MC-" + Date.now(),// your unique merchant reference
                  "meta": [{metaname: "flightID", metavalue: "123949494DC"}],
                  "redirect_url": "https://rave-webhook.herokuapp.com/receivepayment",
                  "device_fingerprint": "69e6b7f0b72037aa8428b70fbe03986c"
                }';
        $this->card = new Card([
                    'cardno' => '5399415500899537',
                    'cvv' => '068',
                    'expirymonth' => '05',
                    'expiryyear' => '22',
                    'token' => null
                ]);
    }

    public function testExample()
    {
        $this->assertTrue(true == true);
    }
    public function testInitPayment(){
        $response = $this->controller->initPayment($this->card, 3009, 1000);//Receive response
        $result = json_decode($response->getBody()->getContents());
        $this->assertEquals("success", $result->data->authModelUsed);
    }
}
