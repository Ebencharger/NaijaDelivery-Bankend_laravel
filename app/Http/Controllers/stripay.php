<?php
namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use URL;
use Session;
use Redirect;
use Input;
use App\User;
use Stripe\Error\Card;
use Cartalyst\Stripe\Stripe;
use Exception;

class Stripay extends Controller
{

public function check_holding(Request $request)
 {

 $validator = $request->validate([
 'card_no' => 'required',
 'ccExpiryMonth' => 'required',
 'ccExpiryYear' => 'required',
 'cvvNumber' => 'required',
 ]);
 if ($validator) { 
    $stripe = new \Stripe\StripeClient(
        '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'
      );
 try {
    $token=$stripe->tokens->create([
        'card' => [
            "number"    => $request->get('card_no'),
            "exp_month" => $request->get('ccExpiryMonth'),
            "exp_year"  => $request->get('ccExpiryYear'),
            "cvc"       => $request->get('cvvNumber'),
        ],
      ]);
if (!isset($token['id'])) {
 return 'no id';
 }
 $charge = $stripe->charges->create([
 'card' => $token['id'],
 'currency' => 'USD',
 'amount' => $request->amount*100,
 'description' => 'wallet',
 ]);
 if($charge['status'] == 'succeeded') {
 return 'money paid';
 } else {
 return 'Money not add in wallet!!';
 }
 } catch (Exception $e) {
 return (['error',$e->getMessage()]);
 } catch(\Stripe\Exception\CardException $e) {
 return (['error',$e->getMessage()]);
 } catch(\Stripe\Exception\InvalidArgumentException $e) {
 return (['error',$e->getMessage()]);
 }
 }
 }
}