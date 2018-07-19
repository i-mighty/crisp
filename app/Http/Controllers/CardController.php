<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardController extends Controller
{
    //
    public function makeCard($request){
        return [
            'cardno' =>  $request->cardno,
            'cvv' => $request->cvv, 
            'expirymonth' => $request->expirymonth,
            'expiryyear' => $request->expiryyear,
        ];
    }
    public function createCard(Request $request){
        $account = auth('api')->user()->account();
		return $account->createCard($this->makeCard($request));
    }
    public function updateCard(Request $request){
        $account = auth('api')->user()->account();
        $account->updateCard($this->makeCard($request), $request->card_id);
    }
}
