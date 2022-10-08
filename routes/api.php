<?php

use App\Http\Controllers\blog;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Mail\message;
use App\Http\Controllers\webapp;
use App\Http\Controllers\mobile;
use App\Http\Controllers\stripay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Salman\GeoCode\Services\GeoCode;
use App\Payment;
use Symfony\Component\HttpFoundation\Response;
use \KMLaravel\GeographicalCalculator\Facade\GeoFacade;
 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//get all rider details
Route::get('/rider', [webapp::class, 'rider_details']);

   //this is where contactus data are sent to database
Route::post('/contactus', [webapp::class, 'contact_us']);

//restaurant data are sent to database
Route::post('/restuarant', [webapp::class, 'restaurant']);

//Get Restuarant data
Route::get('/restuarant', [webapp::class, 'get_restaurant']);

//DELETE Restuarant
Route::post('/deleterestuarant', [webapp::class, 'delete_restaurant']);

//rider data are sent to database
Route::post('/rider', [webapp::class, 'send_rider']);

//Blog data are fetched from here to blog page and its class is found in controller, in blog.php
Route::get('/blog', [blog::class, 'blog']);


//ADMIN CREDENTIAL
Route::get('/adminLogin', [webapp::class, 'admin_login']);

//OPERATIONAL CREDENTIAL
Route::get('/operaLogin', [webapp::class, 'opera_login']);

//CHANGE ADMIN STATUS
Route::post('/adminLogin', function (Request $request) {
    $status = DB::table('admins')->update([
        'status' => $request->myData,
    ]);
    return $status;
});

//CHANGE OPERATIONAL STATUS
Route::post('/operaLogin', function (Request $request) {
    $status = DB::table('operational')->update([
        'status' => $request->myData,
    ]);
    return $status;
});

//EDIT ADMIN PICTURE
Route::post('/adminPicture', [webapp::class, 'admin_picture']);

//EDIT OPERATIONAL Picture
Route::post('/operaPicture',[webapp::class, 'opera_picture']);

//EDIT ADMIN PROFILE
Route::post('/adminProfile', [webapp::class, 'admin_profile']);

//EDIT OPERATIONAL PROFILE
Route::post('/operaProfile', [webapp::class, 'opera_profile']);





//Edit selected order from the database
Route::post('/editOrder', [webapp::class, 'edit_order']);

//Delete selected order from the database
Route::post('/deleteOrder', [webapp::class, 'delete_order']);

//Get orderhistory
Route::post('/riderhistory',[webapp::class, 'rider_history']);

//Post Manual add Rider details
Route::post('/manualrider', [webapp::class, 'manual_rider']);


//Edit selected manually rider form the database
Route::post('/editrider', [webapp::class, 'edit_rider']);

//Delete selected rider manually from the database
Route::post('/deleterider', [webapp::class, 'delete_rider']);

//GET RESTUARANT MENU AND TRENDING MENU PICTURES AND details
Route::post('/menu', [webapp::class, 'res_menu']);

//ALL CUSTOMERS DETAILS
Route::get('/customers', [webapp::class, 'get_customers']);

//GET CUSTOMER HISTORY
Route::post('/customerhistory', [webapp::class, 'customer_history']);

//Post Manual add Customer details
Route::post('/manualcustomer', [webapp::class, 'manual_customer']);

//Edit selected manually rider form the database
Route::post('/editcustomer', [webapp::class, 'edit_customer']);

//Delete selected Customer manually from the database
Route::post('/deletecustomer',[webapp::class, 'delete_customer']);

//get all foods details
Route::get('/foods', [webapp::class, 'get_food']);

//Delete selected Food manually from the database
Route::post('/deletefood', [webapp::class, 'delete_food']);

//GET each food selected details
Route::post('/selectedfood', [webapp::class, 'selected_food']);


//ALL COUPON DETAILS
Route::get('/coupon', [webapp::class, 'get_coupon']);

//Add new coupon to the database
Route::post('/newcoupon', [webapp::class, 'new_coupon']);




//Edit selected coupon form the database
Route::post('/editcoupon', [webapp::class, 'edit_coupon']);

//Delete selected coupon from the database
Route::post('/deletecoupon', [webapp::class, 'delete_coupon']);

//get all refund from the database
Route::get('/refund', [webapp::class, 'get_refund']);

//post refund 
Route::post('/refund', [webapp::class, 'post_refund']);

//Add new refund to the database
Route::post('/newrefund', [webapp::class, 'new_refund']);

//Edit selected refund form the database
Route::post('/editrefund', [webapp::class, 'edit_refund']);

//Delete selected refund from the database
Route::post('/deleterefund', [webapp::class, 'delete_refund']);

//get all reviews...
Route::get('/review', [webapp::class, 'get_review']);

//Delete selected review from the database
Route::post('/deletereview', [webapp::class, 'delete_review']);

//approve rider
Route::post('approverider', [webapp::class, 'approve_rider']);

//approve restaurant
Route::post('approverestaurant', [webapp::class, 'approve_restaurant']);

Route::post('payrider', [webapp::class, 'pay_rider']);



//Prepare restaurant dashboard data
Route::post('/restdashboard',[webapp::class, 'rest_dashboard']);

//delete restaurant account
Route::post('/account_delete', [webapp::class, 'account_delete']);

//update restaurant
Route::post('/update_restaurant', [webapp::class, 'update_restaurant']);

//upload Item
Route::post('/uploaditem', [webapp::class, 'upload_item']);

//update rest food
Route::post('/updaterestfood', [webapp::class, 'update_rest_food']);

//delete rest food
Route::post('/restdeletefood', [webapp::class, 'rest_delete_food']);

//upload food
Route::post('/uploadoffer', [webapp::class, 'upload_offer']);

//delete offer
Route::post('/restdeleteoffer', [webapp::class, 'rest_delete_offer']);

//update restaurant offer
Route::post('/updaterestoffer', [webapp::class, 'update_rest_offer']);

//MAINPAGE ORDER AND DELIVERY BACK END
//get offer list from database
Route::post('/offer', [webapp::class, 'offer']);

//restaurant food
Route::post('/restfood', [webapp::class, 'rest_food']);

//if customer has details, just input his or her order in database
Route::post('/orderone', [webapp::class, 'order_one']);

//if customer has no info yet
Route::post('/ordertwo', [webapp::class, 'order_two']);

Route::post('stripe', function (Request $request) {
    try {
        \Stripe\Stripe::setApiKey(config('app.stripekey'));
        $customer = \Stripe\Customer::create([
            'email' => $request->email,
            'source' => $request->id,
        ]);

        $charge = \Stripe\Charge::create([
            'amount' => round($request->amount * 100), // amount in cents, again
            'currency' => 'usd',
            'customer' => $customer->id,
            'receipt_email' => $request->email,
            'description' => 'Example charge',
        ]);
        $status = 'success';
        $charge->amount = $charge->amount / 100;
        return $charge;

        //   return response(['intent'=>$intent]);
    } catch (\Throwable $e) {
        return response()->json([('errors'), $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
});

Route::get('currency', function () {
    $response = json_decode(Http::get('https://api.currencyfreaks.com/latest?apikey=9a1eb80f833a4c13b40031ae20de6770&symbols=NGN,CAD,EUR,USD'));
    return $response;
});

Route::post('whatsapp', function (Request $request) {
    $response = Http::withToken('EAAEv3BwvmPIBACZBQZBeaRHwmRH3tg8e8SgGMZCfg0dDxaqtn0TCGD2I0tToePVmavCubPL54uEcZBIoK4A15rOqgwP1ZAZABJcPBpZANiakCTOfrubV5mIIVh7iCn5q7YdJrxoOBJg2e6agZAzWg7QHCjEiNgAeDk5Vezphb7MtHXwTaWffevNnDNMMRceUO105IxaXfyJPEwZDZD')->post('https://graph.facebook.com/v13.0/101199345996636/messages', [
        'messaging_product' => 'whatsapp',
        'to' => '2348167302289',
        'type' => 'template',
        'template' => ((object) ['name' => 'hello_world', 'language' => ['code' => 'en_US']]),
    ]);
    return $response;
});



//MOBILE DATA
//rider sign up
Route::post('/mobileriderup', [mobile::class, 'rider_sign']);

//Log out
Route::post('/riderlogout', [mobile::class, 'rider_logout']);

//Generate token pass each hyper operation
Route::post('/passtoken', [mobile::class, 'token']);

//reset password or forgotten password
Route::post('/mobileridereset', [mobile::class,'rider_password_reset']);

//get all riders details ready for login
Route::post('/mobilelogin', [mobile::class, 'riders_details']);

//After rider logged in get his id and used it to get details
Route::post('/mobileridetails', [mobile::class, 'logged_in_details']);

//change rider number
Route::post('/mobilerinumber', [mobile::class, 'rider_change_number']);

//RESET RIDER WITHDRAWAL ACCOUNT
Route::post('/resetwithdrawal', [mobile::class,'rider_reset_withdrawal']);

//Withdraw
Route::post('/withdraw', [mobile::class, 'withdraw']);

//update profile
Route::post('/profile', [mobile::class, 'profile']);

//update location
Route::post('/location', [mobile::class, 'location']);

//start delivery
Route::post('/startdelivery', [mobile::class, 'start_delivery']);

//end delivery
Route::post('/enddelivery', [mobile::class,'end_delivery']);

//cancel order
Route::post('/ridercancelorder', [mobile::class, 'rider_cancel_order']);




//USER'DATA
//user sign up
Route::post('/mobileuserup', [mobile::class,'user_sign']);

//get all users details ready for login
Route::post('/mobuserlogin', [mobile::class, 'user_login']);

//Check this again for orrr
//After user logged in get his id and use it to get details
Route::post('/mobileuserdetails',[mobile::class, 'user_details']);

//update user location
Route::post('/userlocation', [mobile::class, 'update_location']);

//User reset

//reset password or forgotten password
Route::post('/mobileusereset', [mobile::class, 'reset_password']);

//change rider number
Route::post('/mobileusernumber', [mobile::class, 'update_number']);


// check rider availability
Route::post('/check_rider', function(Request $request){
       $dst=0;
       $see=[];
       $almigh=[];
    $rider=DB::table('rider')->get();
    $order=DB::table('order')->get();
    for ($i=0; $i < count($rider); $i++) { 
    $address = $request->pick_up;
    $address2 = $rider[$i]->address.' '.$rider[$i]->location;
    $getPoints = new GeoCode();
    $di='';
    $location=json_decode($getPoints->getLatAndLong($address));
    $location2=json_decode($getPoints->getLatAndLong($address2));
    if (!$location2) {
        $response = json_decode(Http::get('http://127.0.0.1:8000/api/check_rider'));
    }
    $addresslat1=(float)$location->geometry->location->lat;
    $addresslng1=(float)$location->geometry->location->lng;
    $addresslat2=(float)$location2->geometry->location->lat; $addresslng2=(float)$location2->geometry->location->lng;
    $distance=json_encode(GeoFacade::setPoint([$addresslat1,$addresslng1])->setPoint([$addresslat2,$addresslng2])->setOptions(['units' => ['km']])->getDistance());
    $dis=($distance);
    $di=json_decode($dis);
    $see[]=$di;
    }
    $saa=[];
     //monitor this forever;
    // return $almigh->{"dis1-2"};
    $almigh=$see[count($see)-1];
    $a= $almigh;
    $valu=[];
    foreach ($a as $key => $value) {
        $valu[]= $value;
    }

    //pick the first object and other even object; //i bypass setpoint error
    for ($i=0; $i < count($valu); $i++) {
        if ((($i%2)==0) || ($i==0)) {
            $saa[]=$valu[$i];    
        }
    }
     
    //check any available rider;
     for ($i=0; $i < count($saa); $i++) {
        if (($saa[$i]->{'km'}<=6)) {
            for ($j=0; $j < count($order); $j++) { 
                $seen=DB::table('order')->where('userid', $rider[$i]->id)->where('status', 'In Progress')->get();
                if (count($seen)==0) {
                  return ['resp'=>'Rider '.$rider[$i]->name.' found', 'rider_id'=>$rider[$i]->id];
                }
            }
        }
        else if (($i==count($saa)-1) && ($valu[$i]->{'km'}>=6)){
            return 'No Rider is available yet';
        }
     }
});



// send package
Route::post('/send_pack', [mobile::class, 'send_pack']);



  // check out order
Route::post('/checkOut_order', [mobile::class, 'check_out']);






//review
Route::post('/userreview', [mobile::class, 'user_review']);

//top up
Route::post('/usertopup', [mobile::class, 'user_top_up']);

//use wallet
Route::post('/usewallet', [mobile::class, 'use_wallet']);


//check holding
Route::post('/checkholding', [stripay::class, 'check_holding']);

//check if later rider is available
Route::get('/availablenow', [webapp::class, 'is_rider_available']);

//revisit cancelled delivery
Route::get('/revistorder', [webapp::class, 'revisit_cancel_order']);

//loyalty point
Route::get('/loyaltypoint', [webapp::class, 'loyalty_point']);

//use coupon
Route::post('/usecoupon', [mobile::class, 'use_coupon']);

//user cancel order
Route::post('/usercancelorder', [mobile::class, 'user_cancel_order']);

//log out
Route::post('/userlogout', [mobile::class, 'user_logout']);

//call loyaltypoint
//implement holding

// Route::post('geo', function(Request $request){
//     $address = "Alakia Ibadan Nigeria";
//     $address2 = "Seye Popola Ibadan Nigeria";
//     $getPoints = new GeoCode();
//     $di='';
//     $location=json_decode($getPoints->getLatAndLong($address));
//     $location2=json_decode($getPoints->getLatAndLong($address2));
//     $addresslat1=(float)$location->geometry->location->lat;
//     $addresslng1=(float)$location->geometry->location->lng;
//     $addresslat2=(float)$location2->geometry->location->lat; $addresslng2=(float)$location2->geometry->location->lng;
//     $distance=json_encode(GeoFacade::setPoint([$addresslat1,$addresslng1])->setPoint([$addresslat2,$addresslng2])->setOptions(['units' => ['km']])->getDistance());
//     $dis=($distance);
//     if (Str::contains($dis,'1-2')) {
//     $di=substr($dis, 10, 25);
//     }
//     $di=json_decode($di);
//     $distance=(double)(substr($di->km, 0, 5));
//     $distance=round($distance, 2);
//     return $distance;
// });


  





