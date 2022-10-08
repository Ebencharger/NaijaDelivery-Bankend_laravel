<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Mail\message;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
 
});

Route::get('/vir', function(){
    $message="Dear ".","."<br>"."Thank you for your message."."<br><br>"."This is to confirm that we have received your enquiry and we will be endeavour to respond within 48 hours. If this is urgent please contact us on 09066936223 or send email:admin@9jadelivery.com"."<br><br>"."Thank you"."<br>"."Regards,"."<br><br>"."Operations team";
    $data=['name'=>"EBENERZER", 'subject'=>'NO RESPONSE REPLY', 'view'=>'alert', 'message'=>$message];
    $r=new message($data);
    Mail::to('ajayioluwaseunebenezer@gmail.com')->send($r);
    return view('alert')->with('data', $data);
});
