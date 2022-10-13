<?php

namespace App\Http\Controllers;
use App\Http\Controllers\blog;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Mail\message;
use App\Http\Controllers\webapp;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class mobile extends Controller
{
    //

    //RIDER 

    //mobile rider sign up
    public function rider_sign(Request $request) {
        $token = mt_rand(1000, 9000);
        $date = Carbon::now();
        $monthName = $date->format('F') . ' ' . $date->format('Y');
        $time = $date->format('G:ia');
        $completeYear = $date->format('d/m/Y');
        $result = DB::table('rider')->insert([
            'name' => $request->first_name.' '.$request->other_name,
            'location' => $request->location,
            'phone' => '',
            'email' => $request->email,
            'password' => $request->password,
            'token' => $token,
            'picture' => 'images/default.jpg',
            'status' => 'Pending',
            'balance'=>0,
            'company' => 'nil',
            'lasttime' => 'nil',
            'lastdate' => 'nil',
            'lastseen' => 'nil',
            'available' => 'nil',
            'delivertime' => 'nil',
            'bio' => 'nil',
            'transactionDate' => $monthName,
            'address' => '',
            'why' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $message1 = 'Dear Admin' . ',' . '<br>' . 'This is to notify you that a new Rider registered with' . $request->email . '<br><br>' . '<br>' . 'Regards';
        $message = 'Dear ' . $request->email . ',' . '<br>' . 'Thank you for registering with us.' . '<br><br>' . 'This is to confirm that we have received your details and we will be endeavour to respond within 48 hours. To reach our desk urgently please contact us on 09066936223 or send email:admin@9jadelivery.com' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
        $data1 = ['name' => 'Admin', 'subject' => 'FEEDBACK FROM MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message1];
        $data = ['name' => $request->email, 'subject' => 'NO RESPONSE REPLY', 'view' => 'alert', 'message' => $message];
        $r1 = new message($data1);
        $r = new message($data);
        Mail::to('admin@9jadelivery.com')->send($r1);
        Mail::to($request->email)->send($r);
        if ($result) {
            DB::table('activity')->insert([
                'restid' => '',
                'what' => $time . ', ' . $completeYear . ': ' . $request->email . ' registered as a Rider',
            ]);
            return 'success';
        } else {
            return 'error';
        }
    }

    //generate token
    public function token(Request $request) {
        $email = json_decode(
            DB::table('rider')
                ->where('id', $request->id)
                ->get(),
        );
        $date = Carbon::now();
        $monthName = $date->format('F') . ' ' . $date->format('Y');
        $time = $date->format('G:ia');
        $completeYear = $date->format('d/m/Y');
        $token = mt_rand(1000, 5000);
        if (
            count(
                DB::table('tokencheck')
                    ->where('rider_id', $request->id)
                    ->get()
            ) >= 1
        ) {
            $post = DB::table('tokencheck')->where('rider_id', $request->id)->update([
                'token' => $token,
            ]);
        } elseif (
            count(
                DB::table('tokencheck')
                    ->where('rider_id', $request->id)
                    ->get()
            ) <= 0
        ) {
            $post = DB::table('tokencheck')->where('rider_id', $request->id)->insert([
                'rider_id' => $request->id,
                'token' => $token,
            ]);
        }
        if ($post) {
            DB::table('activity')->insert([
                'restid' => '',
                'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . ' generated a new token to perform operation',
            ]);
            $message = 'Dear ' . $email[0]->name . ',' . '<br>' . 'This is your token.' . '<h1>' . $token . '</h1>' . '. Ensure you keep it safe. To reach our desk urgently please contact us on 09066936223 or send email:admin@9jadelivery.com' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
            $data = ['name' => $email[0]->email, 'subject' => 'NO RESPONSE REPLY', 'view' => 'alert', 'message' => $message];
            $r = new message($data);
            Mail::to($email[0]->email)->send($r);
            return 'success';
        }
        else if(!$post){
            return 'error';
        }
    }

    //reset rider password
    public function rider_password_reset(Request $request) {
        $email = json_decode(
            DB::table('rider')
                ->where('id', $request->id)
                ->get('email'),
        );
        $date = Carbon::now();
        $monthName = $date->format('F') . ' ' . $date->format('Y');
        $time = $date->format('G:ia');
        $completeYear = $date->format('d/m/Y');
        $check_token = DB::table('tokencheck')
            ->where([['token', $request->token], ['rider_id', $request->id]])
            ->get();
        if (count($check_token) >= 1) {
            $success = DB::table('rider')
                ->where('id', $request->id)
                ->update([
                    'password' => $request->password,
                ]);
            if ($success) {
                $sus = DB::table('activity')->insert([
                    'restid' => '',
                    'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . ' changed his password',
                ]);
                return 'success';
            } elseif (!$success) {
                return 'error';
            }
        } elseif (count($check_token) < 1) {
            return 'invalid token';
        }
    }

    //get all riders details ready for login
    public function riders_details(Request $request) {
        $rider = DB::table('rider')->where('status', 'Approved')->get();
        // $user=DB::table('customers')->get();
        $allUser = [];
        for ($i = 0; $i < count($rider); $i++) {
            $rider[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $rider[$i]->picture;
            $allUser[] = $rider[$i];
        }
        return $allUser;
    }

    //rider log out
    public function rider_logout(Request $request){
        $date=Carbon::now();
        $logout=DB::table('rider')
            ->where('id', $request->id)
            ->update([
                'lasttime'=>$date->format('G:ia').','.$date->format('d/m/Y'),
                'lastseen'=>$date->format('G:ia').','.$date->format('d/m/Y'),
                'lastdate'=>$date->format('d/m/Y'),
                'available'=>'Offline',
            ]);
            if ($logout) {
               return (['message', 'You are logged out']);
            }
    }

   //get a logged in rider details
   public function logged_in_details(Request $request) {
    $riderData = [];
    $ongoing=[];
    $cancel=[];
    $history=[];
    $credit=[];
    $withdrawal_details=DB::table('withdrawal_account')->where('id', $request->id)
    ->get();
    $credit= DB::table('credit')->where('riderid', $request->id)->get();
    $history = DB::table('order')
        ->where([['userid', $request->id], ['status', 'Delivered']])
        ->get();
        for ($i=0; $i <count($history) ; $i++) { 
       $pic=DB::table('customers')->where('id', $history[$i]->customerid)->get('picture');
       $repic='https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' .$pic[0]->picture;
       $history[$i]->userpic=$repic;
       $name=DB::table('customers')->where('id', $history[$i]->customerid)->get('name');
       $rename=$name[0]->name;
       $history[$i]->username=$rename;
       $phone=DB::table('customers')->where('id', $history[$i]->customerid)->get('phone');
       $rephone=$phone[0]->phone;
       $history[$i]->userphone=$rephone;
    }

    $ongoing = DB::table('order')
        ->where([['userid', $request->id], ['status', 'Pending']])
        ->get();

    for ($i=0; $i <count($ongoing) ; $i++) { 
       $pic=DB::table('customers')->where('id', $ongoing[$i]->customerid)->get('picture');
       $repic='https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' .$pic[0]->picture;
       $ongoing[$i]->userpic=$repic;

       $name=DB::table('customers')->where('id', $ongoing[$i]->customerid)->get('name');
       $rename=$name[0]->name;
       $ongoing[$i]->username=$rename;
       $phone=DB::table('customers')->where('id', $ongoing[$i]->customerid)->get('phone');
       $rephone=$phone[0]->phone;
       $ongoing[$i]->userphone=$rephone;
    }
    $cancel = DB::table('rider_cancel')
        ->where([['riderid', $request->id]])
        ->get();

    //     for ($i=0; $i <count($cancel) ; $i++) { 
    //    $pic=DB::table('customers')->where('id', $cancel[$i]->customerid)->get('picture');
    //    $repic='https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' .$pic[0]->picture;
    //    $cancel[$i]->userpic=$repic;
    //    $name=DB::table('customers')->where('id', $cancel[$i]->customerid)->get('name');
    //    $rename=$name[0]->name;
    //    $cancel[$i]->username=$rename;
    //    $phone=DB::table('customers')->where('id', $cancel[$i]->customerid)->get('phone');
    //    $rephone=$phone[0]->phone;
    //    $cancel[$i]->userphone=$rephone;
    // }

    $withdraw = DB::table('withdrawal')
        ->where([['rider_id', $request->id]])
        ->get();
    $notification = DB::table('ridernoti')
        ->where([['rider_id', $request->id]])
        ->get();
    $riderData = ['history' => $history, 'ongoing' => $ongoing, 'cancel' => $cancel, 'withdrawal' => $withdraw, 'notification' => $notification, 'withdrawal_details'=>$withdrawal_details,'credit'=>$credit];
    $check = DB::table('rider')
        ->where('id', $request->id)
        ->get();
    if (count($check) >= 1) {
        DB::table('rider')
        ->where('id', $request->id)
        ->update([
            'available'=>'Online'
        ]);
        return $riderData;
    } elseif (count($check) == 0) {
        return 'error';
    }
}

//rider change number
 public function rider_change_number (Request $request) {
    //get server ip address
    //return \Request::ip();
    $email = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('email'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $check_token = DB::table('tokencheck')
        ->where([['token', $request->token], ['rider_id', $request->id]])
        ->get();
    if (count($check_token) >= 1) {
        $success = DB::table('rider')
            ->where('id', $request->id)
            ->update([
                'phone' => $request->phone,
            ]);
    }
    if ($success) {
        DB::table('ridernoti')->insert([
            'rider_id' => $request->id,
            'what' => $time . ', ' . $completeYear . ': You change your account phone number',
        ]);
        $sus = DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . ' changed his mobile number',
        ]);
        return 'success';
    } elseif (!$success) {
        return 'error';
    } elseif (count($check_token) <= 1) {
        return 'invalid token';
    }
}

//reset rider withdrawal account
public function rider_reset_withdrawal(Request $request) {
    $email = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('email'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $check_token = DB::table('tokencheck')
        ->where([['token', $request->token], ['rider_id', $request->id]])
        ->get();
    if (count($check_token) >= 1) {
        if (
            count(
                DB::table('withdrawal_account')
                    ->where('id', $request->id)
                    ->get(),
            ) >= 1
        ) {
            $success = DB::table('withdrawal_account')
                ->where('id', $request->id)
                ->update([
                    'acc_name' => $request->acc_name,
                    'acc_number' => $request->acc_number,
                    'bank_name' => $request->bank_name,
                ]);
        } else {
            $success = DB::table('withdrawal_account')->insert([
                'id' => $request->id,
                'acc_name' => $request->acc_name,
                'acc_number' => $request->acc_number,
                'bank_name' => $request->bank_name,
            ]);
        }
        if ($success) {
            DB::table('ridernoti')->insert([
                'rider_id' => $request->id,
                'what' => $time . ', ' . $completeYear . ': You updated your withdrawal account',
            ]);
            $sus = DB::table('activity')->insert([
                'restid' => '',
                'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . ' changed his password',
            ]);
            return 'success';
        } elseif (!$success) {
            return 'error';
        }
    } elseif (count($check_token) < 1) {
        return 'invalid token';
    }
}

//withdraw
public function withdraw(Request $request) {
    $email = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('email'),
    );
    $bal = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('balance'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $check_token = DB::table('tokencheck')
        ->where([['token', $request->token], ['rider_id', $request->id]])
        ->get();
    if (count($check_token) >= 1) {
       DB::table('rider')
            ->where('id', $request->id)
            ->update([
            'balance'=>$bal[0]->balance-$request->amount
         ]);
        $success = DB::table('withdrawal')->insert([
            'rider_id' => $request->id,
            'amount' => $request->amount,
            'date' => $time . ', ' . $completeYear,
            'status' => 'Pending',
        ]);
        if ($success) {
            DB::table('ridernoti')->insert([
                'rider_id' => $request->id,
                'what' => $time . ', ' . $completeYear . ': You sent withdrawal',
            ]);
            $sus = DB::table('activity')->insert([
                'restid' => '',
                'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . ' changed his password',
            ]);
            return 'success';
        } elseif ($success) {
            return 'error';
        }
    } elseif (count($check_token) < 1) {
        return 'invalid token';
    }
}

//update profile
public function profile(Request $request) {
    $email = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('email'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    if (Str::contains($request->image, 'wamp') || Str::contains($request->image, 'tmp')) {
        $imageName = mt_rand(1000, 9000);
        $extension = $request->image->extension();
        $image = $imageName . '.' . $extension;
        $myimage = 'images';
        $saveImage = $myimage . '/' . $image;
        $path = $request->image->storeAs('public/' . $myimage, $image);
    } else {
        if(Str::contains($request->image, 'public')){
            $count=strlen($request->image);
            $saveImage=substr($request->image, 68, $count);
        }
    }
    $check_token = DB::table('tokencheck')
        ->where([['token', $request->token], ['rider_id', $request->id]])
        ->get();
    if (count($check_token) >= 1) {
        $success = DB::table('rider')
            ->where('id', $request->id)
            ->update([
                'picture' => $saveImage,
                'name' => $request->sur_name . ' ' . $request->other_name,
                'company' => '',
                'bio' => $request->bio,
                'why' => '',
                'address' =>'',
            ]);
        if ($success) {
            DB::table('ridernoti')->insert([
                'rider_id' => $request->id,
                'what' => $time . ', ' . $completeYear . ': You updated profile',
            ]);
            $sus = DB::table('activity')->insert([
                'restid' => '',
                'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . ' updated his profile',
            ]);
            return 'success';
        } elseif ($success) {
            return 'error';
        }
    } elseif (count($check_token) < 1) {
        return 'invalid token';
    }
}

//update location
public function location(Request $request) {
    $email = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('email'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $success = DB::table('rider')
        ->where('id', $request->id)
        ->update([
            'location' => $request->location,
        ]);
    if ($success) {
        DB::table('ridernoti')->insert([
            'rider_id' => $request->id,
            'what' => $time . ', ' . $completeYear . ': You changed your location',
        ]);
        $sus = DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . 'changed his location',
        ]);
        return 'success';
    } elseif ($success) {
        return 'error';
    }
}

//start delivery
public function start_delivery(Request $request){
    $email = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('email'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $initialized = DB::table('order')
        ->where([['userid', $request->id], ['status', 'Pending'], ['ordernum', $request->order_num]])
        ->update([
            'status'=>'In Progress',
            'pickuptime'=>$time,
            'pickup'=>$request->pick_up
        ]);
    if ($initialized) {
        DB::table('transaction')->where([['userid', $request->id], ['status', 'Pending'], ['orderid', $request->order_num]])
        ->update([
            'riderarrive'=>$time,
            'pickup'=>$time,
        ]);
        $sus = DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . 'Starts delivery',
        ]);
        return "success";
    }
    else{
        return 'error';
    }
}

//end delivery
public function end_delivery(Request $request){
    $fee=$request->fee-($request->fee*(20/100));
    $email = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('email'),
    );
    $balance = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('balance'),
    );
     $deliverCount = json_decode(
        DB::table('rider')
            ->where('id', $request->id)
            ->get('delivertime'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
$check=DB::table('order')->where([['userid', $request->id], ['status', 'In Progress'], ['ordernum', $request->code]])->get();
    if (count($check)>=1) {
        $initialized = DB::table('order')
        ->where([['userid', $request->id], ['status', 'In Progress'], ['ordernum', $request->code]])
        ->update([
            'status'=>'Delivered',
            'delivertime'=>$time,
            'deliverto'=>$request->deliver_to
    ]);
    if ($initialized) {
        DB::table('transaction')->where([['userid', $request->id], ['status', 'In Progress'], ['orderid', $request->order_num]])
        ->update([
            'status'=>'Delivered', 
        ]);
        $sus = DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . 'Ends Delivery Successfully',
        ]);
       
        $sus = DB::table('rider')->where('id', $request->id)->update([
            'delivertime'=>$deliverCount[0]->delivertime+1,
            'balance' => $balance[0]->balance + $fee,
        ]);
        DB::table('credit')->insert([
            'riderid' => $request->id,
            'amount'=>$fee,
            'date'=>$time.', '.$completeYear,
            ]);
        $sus = DB::table('riderhistory')->insert([
            'riderid' => $request->id,
            'address' => $request->deliver_to,
            'date'=>$completeYear,
            'time'=>$time,
            'fee'=>$fee,
            'status'=>'Delivered'
        ]);
        return "success";
    }
    else{
        return 'error';
    }
    }
    else if(count($check)<=0){
        return 'Invalid Transaction Code';
    }
}

//RIDER CANCEL ORDER
public function rider_cancel_order(Request $request){
    $email=DB::table('rider')->where('id', $request->id)->get('email');
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $check=DB::table('rider_cancel')->where('riderid', $request->id)->get();
    if(count($check)>=1){
    for ($i=0; $i <count($check) ; $i++) { 
        if ((count($check)==3) && Carbon::parse($check[$i]->date)->diffInMinutes($completeYear)<=7) {
            $message = 'Dear ' . $email[0]->email . ',' . '<br>' . 'Thank you for working with us.' . '<br><br>' . 'This is to notify you that your account has been put on hold. Kindly reach our desk urgently by contacting us on 09066936223 or send email:admin@9jadelivery.com' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
        $data = ['name' => $email[0]->email, 'subject' => 'NO RESPONSE REPLY', 'view' => 'alert', 'message' => $message];
        $r = new message($data);
        Mail::to($email[0]->email)->send($r);
        DB::table('rider')->where('id', $request->id)->update(
            [
                'status'=>'Pending'
            ]
            );
        return 'You have been pushed out of our database, contact Admin';
        }
        else if($i==count($check)-1 && count($check)<3){
            $inside=DB::table('rider_cancel')->insert([
                'riderid'=>$request->id,
                'ordernum'=>$request->ordernum,
                'date'=>$completeYear
             ]);
             if($inside){
        DB::table('order')
        ->where([['userid', $request->id], ['ordernum', $request->ordernum]])
        ->update([
            'status'=>'Pending',
            'userid'=>'',
    ]);
    DB::table('transaction')->where([['riderid', $request->id], ['orderid', $request->ordernum]])
    ->update([
        'status'=>'Pending', 
        'riderid'=>'0', 
    ]);
                DB::table('activity')->insert([
                    'restid' => '',
                    'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . 'Cancelled ongoing Delivery',
                ]);
              return 'You cancelled the ongoing Delivery';
             }else{
              return 'Something went wrong';
             }
        }
    }
    }
    else if(count($check)==0){
        $inside=DB::table('rider_cancel')->insert([
            'riderid'=>$request->id,
            'ordernum'=>$request->ordernum,
            'date'=>$completeYear
         ]);
         if($inside){
            DB::table('order')
            ->where([['userid', $request->id], ['ordernum', $request->ordernum]])
            ->update([
                'status'=>'Pending',
                'userid'=>'',
        ]);
    
        DB::table('transaction')->where([['riderid', $request->id], ['orderid', $request->ordernum]])
        ->update([
            'status'=>'Pending', 
            'riderid'=>'0', 
        ]);
            DB::table('activity')->insert([
                'restid' => '',
                'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . 'Cancelled ongoing Delivery',
            ]);
          return 'You cancelled the ongoing Delivery';
         }else{
          return 'Something went wrong';
         }
    }
   
} 

//delete account
public function rider_delete_account(Request $request){
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $rider_name=DB::table('rider')->where('id', $request->id)->get('name');
    $success=DB::table('rider')->where('id', $request->id)->delete();
    if($success){
         DB::table('activity')->insert([
                    'restid' => '',
                    'what' => $time . ', ' . $completeYear . ': ' . $rider_name[0]->name . ' Deleted his Rider account',
                ]);
        DB::table('riderdeleteaccount')->insert([
            'account_id'=>$request->id,
            'account_name'=>$rider_name[0]->name,
            'why'=>$request->why,
            'date'=>$time.','.$completeYear
            ]);
        return 'You deleted your account';
    }else{
        return 'Something went wrong';
    }
    
}




//USER
//sign up
public function user_sign(Request $request) {
    $token = mt_rand(1000, 9000);
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $result = DB::table('customers')->insert([
        'name' => $request->first_name.' '.$request->other_name,
        'location' => $request->location,
        'phone' => '',
        'email' => $request->email,
        'password' => $request->password,
        'picture' => 'images/default.jpg',
        'status' => 'Pending',
        'lasttime' => 'nil',
        'lastdate' => 'nil',
        'lastseen' => 'nil',
        'available' => 'nil',
        'bio' => 'nil',
        'transactionDate' => $monthName,
        'address' => '',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $message1 = 'Dear Admin' . ',' . '<br>' . 'This is to notify you that a new user registered with' . $request->email . '<br><br>' . '<br>' . 'Regards';
    $message = 'Dear ' . $request->email . ',' . '<br>' . 'Thank you for registering with us.' . '<br><br>' . 'This is to confirm that we have received your details and thanks for registering with us. To reach our desk urgently please contact us on 09066936223 or send email:admin@9jadelivery.com' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
    $data1 = ['name' => 'Admin', 'subject' => 'FEEDBACK FROM MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message1];
    $data = ['name' => $request->email, 'subject' => 'NO RESPONSE REPLY', 'view' => 'alert', 'message' => $message];
    $r1 = new message($data1);
    $r = new message($data);
    Mail::to('admin@9jadelivery.com')->send($r1);
    Mail::to($request->email)->send($r);
    if ($result) {
        $allcust=DB::table('customers')->get();
        DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': ' . $request->email . ' registered as a User',
        ]);
        DB::table('userwallet')->insert([
            'user_id'=>$allcust[count($allcust)-1]->id,
            'balance'=>0
      ]);
        return 'success';
    } else {
        return 'error';
    }
}

//user login
public function user_login(Request $request) {
    $user = DB::table('customers')->get();
    // $user=DB::table('customers')->get();
    $allUser = [];
    for ($i = 0; $i < count($user); $i++) {
        $user[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $user[$i]->picture;
        $allUser[] = $user[$i];
    }
    return $allUser;
}

//user details
public function user_details(Request $request) {
    $userData = [];
    $ongoing=[];
    $wallet=[];
    $completed=[];
    $restaurant=[];
    $history=[];
    $notification=[];
    $food=[];
    $points=0;
    $wallet_history=[];
    $review=[];
    $user=[];
    $loyalty_point=[];
    $history = DB::table('order')
        ->where([['customerid', $request->id], ['status', 'Delivered']])
        ->get();

    $loyalty_point=DB::table('coupon')->where([['customerid',$request->id], ['status', 'Active']])->get();

    foreach ($loyalty_point as $key => $valu) {
        $points=$points+$valu->amount;
    } 

    $ongoing = DB::table('order')
        ->where([['customerid', $request->id], ['status', 'Pending']])->orWhere([['customerid', $request->id], ['status', 'In Progress']])
        ->get();

    $food=DB::table('food')->get();
    $restaurant=DB::table('restuarant')->get();
    $review=DB::table('userreview')->get();
    $user=DB::table('customers')->get();
    $wallet=DB::table('userwallet')->where('user_id', $request->id)->get();
    $wallet_history=DB::table('user_wallet_history')->where('user_id', $request->id)->get();

    for ($i=0; $i <count($food) ; $i++) { 
        $food[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $food[$i]->picture;
        for ($j=0; $j < count($restaurant); $j++) { 
            if ($restaurant[$j]->id==$food[$i]->restuarant) {
            $food[$i]->restaurant = $restaurant[$j]->name;
            $food[$i]->location = $restaurant[$j]->location;
            $food[$i]->address = $restaurant[$j]->address;
            $food[$i]->wallpaper = $restaurant[$j]->wallpaper;
            }
        }
    }

    if (count($review)>0) {
        for ($i=0; $i <count($review) ; $i++) { 
        for ($j=0; $j <count($user) ; $j++) { 
            if ($review[$i]->user_id==$user[$j]->id) {
               $review[$i]->picture='https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $user[$j]->picture;
            }
        }
    }
    }

    $notification = DB::table('usernoti')
        ->where([['user_id', $request->id]])
        ->get();

    $userData = [ 'ongoing' => $ongoing,'completed'=>$history, 'notification'=>$notification, 'food'=>$food, 'review'=>$review, 'wallet'=>$wallet[0]->balance, 'wallet_history'=>$wallet_history, 'loyalty_point'=>$points];

    $check = DB::table('customers')
        ->where('id', $request->id)
        ->get();
    if (count($check) >= 1) {
        DB::table('customers')
        ->where('id', $request->id)
        ->update([
            'available'=>'Online'
        ]);
        return $userData;
    } elseif (count($check) == 0) {
        return 'error';
    }
}

//update location
public function update_location(Request $request) {
    $email = json_decode(
        DB::table('customers')
            ->where('id', $request->id)
            ->get('email'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $success = DB::table('customers')
        ->where('id', $request->id)
        ->update([
            'location' => $request->location,
        ]);
    if ($success) {
        $sus = DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . 'changed his location',
        ]);
        DB::table('usernoti')->insert([
            'user_id' => $request->id,
            'what' => $time . ', ' . $completeYear . ': ' .'You changed your location',
        ]);
        return 'success';
    } elseif ($success) {
        return 'error';
    }
}

//update password
public function reset_password(Request $request) {
    $email = json_decode(
        DB::table('customers')
            ->where('id', $request->id)
            ->get('email'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $check_token = DB::table('tokencheck')
        ->where([['token', $request->token], ['rider_id', $request->id]])
        ->get();
    if (count($check_token) >= 1) {
        $success = DB::table('customers')
            ->where('id', $request->id)
            ->update([
                'password' => $request->password,
            ]);
        if ($success) {
            DB::table('activity')->insert([
                'restid' => '',
                'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . ' changed his password',
            ]);
            DB::table('usernoti')->insert([
            'user_id' => $request->id,
            'what' => $time . ', ' . $completeYear . ': ' .'You changed your password',
        ]);
            return 'success';
        } elseif (!$success) {
            return 'error';
        }
    } elseif (count($check_token) < 1) {
        return 'invalid token';
    }
}

//update phone number
public function update_number(Request $request) {
    //get server ip address
    //return \Request::ip();
    $email = json_decode(
        DB::table('customers')
            ->where('id', $request->id)
            ->get('email'),
    );
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $check_token = DB::table('tokencheck')
        ->where([['token', $request->token], ['rider_id', $request->id]])
        ->get();
    if (count($check_token) >= 1) {
        $success = DB::table('customers')
            ->where('id', $request->id)
            ->update([
                'phone' => $request->phone,
            ]);
    }
    if ($success) {
        DB::table('usernoti')->insert([
            'rider_id' => $request->id,
            'what' => $time . ', ' . $completeYear . ': You change your phone number',
        ]);
        $sus = DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': ' . $email[0]->email . ' changed his mobile number',
        ]);
        return 'success';
    } elseif (!$success) {
        return 'error';
    } elseif (count($check_token) <= 1) {
        return 'invalid token';
    }
}

//send package
public function send_pack(Request $request){
    $date = Carbon::now();
    $year = $date->format('Y');
    $orderNum = '#' . mt_rand(100000, 900000);
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $transYear = $date->format('F') . ' ' . $date->format('Y');
    $rider_id='';
    DB::table('activity')->insert([
   'restid'=>'',
   'what'=>$time.', '.$completeYear.': A user with id-'.$request->id.' wants to deliver a product'
    ]);
    if ($request->rider_id!='') {
       $rider_id=$request->rider_id;
    }else{
        $rider_id='';
        DB::table('holding')->insert([
        'userid'=>$request->id,
        'ordernum'=>$orderNum,
        'card_no'=>$request->card_no,
        'ccExpiryMonth'=>$request->ccExpiryMonth,
        'ccExpiryYear'=>$request->ccExpiryYear,
        'cvvNumber'=>$request->cvvNumber,
        'amount'=>$request->totalFee
        ]);
    }
 DB::table('order')->insert([
            'userid' => $rider_id,
            'customerid' => $request->id,
            'restid' => 'nil',
            'date' => $completeYear,
            'time' => $time,
            'year' => $year,
            'ordernum' => $orderNum,
            'productweight' => $request->packweight,
            'producttype' => $request->packType,
            'servicefee' => $request->serviceFee,
            'bookingtype' => $request->bookingType,
            'note' => 'nil',
            'distance' => 'nil',
            'pickup' => $request->senderAddress . ',' . $request->senderCountry . ',' . $request->senderPhone,
            'pickuptime' => '',
            'Status' => 'Pending',
            'recipentname'=>$request->receiverName,
             'recipentphone'=>$request->receiverPhone,
            'deliverto' => $request->receiverAddress . ',' . $request->receiverCountry . ',' . $request->receiverPhone,
            'delivertime' => '',
            'transactionDate' => $transYear,
        ]);
        DB::table('transaction')->insert([
            'userid' => $request->id,
            'restid' => '0',
            'riderid' => $rider_id,
            'productname' => $request->packType,
            'productimage' => '',
            'price' => '',
            'quantity' => '',
            'orderid' => $orderNum,
            'riderarrive'=>'',
            'pickup'=>'',
            'amount' =>'',
            'status' => 'Pending',
            'subtotal' => '',
             'deliveryfee' => $request->deliveryFee,
            'discount' => 0.24,
            'date' => $completeYear,
            'transactionDate' => $transYear,
        ]); 
    //to admin
    $message = 'This is to notify you that a customer wants to send a package' . $request->senderPhone . ' and we need one of our riders to reach out as soon as possible. Check Dashboard for more details';
    $data = ['name' => '9jadelivery', 'subject' => 'MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message];
    $r1 = new message($data);
    Mail::to('admin@9jadelivery.com')->send($r1); 
    return 'success';  
  }



  //check out order
  public function check_out(Request $request){
    $date = Carbon::now();
    $year = $date->format('Y');
    $orderNum = '#' . mt_rand(100000, 900000);
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $transYear = $date->format('F') . ' ' . $date->format('Y');
    $customers = DB::table('customers')->get();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $rider_id='';
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
   'restid'=>'',
   'what'=>$time.', '.$completeYear.': A user with id-'.$request->id.' just placed an order'
    ]);
    if ($request->rider_id!='') {
       $rider_id=$request->rider_id;
    }else{
        $rider_id='';
        DB::table('holding')->insert([
        'userid'=>$request->id,
        'ordernum'=>$orderNum,
        'card_no'=>$request->card_no,
        'ccExpiryMonth'=>$request->ccExpiryMonth,
        'ccExpiryYear'=>$request->ccExpiryYear,
        'cvvNumber'=>$request->cvvNumber,
        'amount'=>$request->totalFee
        ]);
    }
    $product = json_decode($request->products);
        foreach ($product as $key => $value) {
            $serviceFee = json_decode($value->addition->total_amount);
            $productType = $value->item_selected->item_name;
            $pickup = $value->pickup;
            $restid = $value->restid;
               $image='';
            $count=strlen($value->item_selected->picture);
       if (Str::contains($value->item_selected->picture, 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/')) {
            $image=substr($value->item_selected->picture, 68, $count);
             }
            DB::table('order')->insert([
                'userid' => $rider_id,
                'customerid' => $request->id,
                'restid' => $restid,
                'date' => $completeYear,
                'time' => $time,
                'year' => $year,
                'ordernum' => $orderNum,
                'productweight' => 'nil',
                'producttype' => 'food',
                'servicefee' => $serviceFee,
                'bookingtype' => 'nil',
                'note' => 'nil',
                'distance' => 'nil',
                'pickup' => $pickup,
                'pickuptime' => $time,
                'Status' => 'Pending',
                'recipentname'=>$request->receiverName,
             'recipentphone'=>$request->receiverPhone,
                'deliverto' => $request->address,
                'delivertime' => $request->$time,
                'transactionDate' => $transYear,
            ]);
            DB::table('transaction')->insert([
                'userid' => $request->id,
                'restid' => $restid,
                'riderid' => $rider_id,
                'productname' => $productType,
                'productimage' => $image,
                'price' => $serviceFee,
                'quantity' => 1,
                'orderid' => $orderNum,
                'riderarrive'=>'',
                'pickup'=>'',
                'amount' => $serviceFee,
                'status' => 'Pending',
                'subtotal' => 1 * $serviceFee,
                'deliveryfee' => $request->deliveryFee,
                'discount' => 0.24,
                'date' => $completeYear,
                'transactionDate' => $transYear,
            ]);
        }
         //to customer
    $message = 'Thank you for your patronage.' . '<br><br>' . 'This is to confirm that we have received your order and one of our riders will reach you out soon. If you do not get any response from us in five minutes time, urgently please contact us on 09066936223 or send email:admin@9jadelivery.com' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
    $data = ['name' => $request->email, 'subject' => 'NO RESPONSE REPLY', 'view' => 'alert', 'message' => $message];
    $r = new message($data);
    Mail::to($request->email)->send($r);

    //to admin
    $message1 = 'This is to notify you that a customer just ordered for' . ' and we need one of our riders to reach out as soon as possible. Check Dashboard for more details. This is the customer details:-' . $request->address . ', ' . $request->phone . ', ' . $request->city . ', ' . $request->country;
    $data1 = ['name' => '9jadelivery', 'subject' => 'MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message1];
    $r1 = new message($data1);
    Mail::to('admin@9jadelivery.com')->send($r1);
    return 'success';
}

//user review
public function user_review(Request $request){
    $post=DB::table('userreview')->where('user_id', $request->id)->insert([
    'user_name'=>$request->user_name,
    'user_id'=>$request->id,
    'comment'=>$request->comment,
    'star'=>$request->star
    ]);
    if ($post) {
        return 'success';
    }else{
        return 'something went wrong, try again';
    }
}



//top up
public function user_top_up(Request $request){
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
 $post=DB::table('user_wallet_history')->where('user_id', $request->id)->insert([
   'user_id'=>$request->id,
   'type'=>'Credit',
   'what'=>'Top up',
    'amount'=>$request->amount,
    'date'=>$time . ', ' . $completeYear,
]);
  if($post){
    $wallet=DB::table('userwallet')->where('user_id', $request->id)->get();
    DB::table('userwallet')->where('user_id', $request->id)->update([
        'balance'=>$wallet[0]->balance+$request->amount
  ]);
    return 'success';
    }
else{
   return 'Something went wrong, but we got you covered!';
}
}

//use wallet instead
public function use_wallet(Request $request){
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
 $post=DB::table('user_wallet_history')->where('user_id', $request->id)->insert([
   'user_id'=>$request->id,
   'type'=>'Debit',
   'what'=>'You used your wallet for transaction',
    'amount'=>$request->amount,
    'date'=>$time . ', ' . $completeYear,
]);
  if($post){
    $wallet=DB::table('userwallet')->where('user_id', $request->id)->get();
    DB::table('userwallet')->where('user_id', $request->id)->update([
        'balance'=>$wallet[0]->balance-$request->amount
  ]);
    return 'success';
    }
else{
   return 'Something went wrong, but we got you covered!';
}
}

//use coupon
public function use_coupon(Request $request){
    $loyalty_point=DB::table('coupon')->where('customerid',$request->id)->get();
    if(count($loyalty_point)>=1){
       $update=DB::table('coupon')->where('coupon_code', $request->coupon_code)->where('status', 'Active')->update([
            'status'=> 'Used',
            'expiry_date'=>now()
        ]);
        if($update){
            return 'Coupon is valid';
        }
        else{
            return 'This Coupon appeared to be invalid';
        }
       
    }else{
        return 'This Coupon does not exist';
    }
}

//user cancel order
public function user_cancel_order(Request $request){
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $amount=0;
    $transact=DB::table('transaction')->where('orderid', $request->ordernum)->get();
 $order=DB::table('order')->where([['ordernum', $request->ordernum], ['status', 'In Progress']])->orWhere([['ordernum', $request->ordernum], ['status', 'Pending']])->get();

 //calculate refund percentage
 for ($i=0; $i <count($transact) ; $i++) { 
    $amount=$amount+$transact[$i]->subtotal;
 }
 $amount=$amount+$transact[0]->deliveryfee;

 if ($order[0]->status=='In Progress') {
 if (Str::contains($order[0]->pickuptime, 'am')) {
    $order[0]->pickuptime=substr($order[0]->pickuptime, 0, 5);
 }
 else if(Str::contains($order[0]->pickuptime, 'pm')){
    $order[0]->pickuptime=substr($order[0]->pickuptime, 0, 5);
 }
 if (Carbon::parse($order[0]->pickuptime)->diffInMinutes($date->format('G:i'))<=30) {
    DB::table('order')->where('ordernum', $request->ordernum)->update(
       [ 
        'status'=>'Failed'
       ]
    );
    DB::table('transaction')->where('orderid', $request->ordernum)->update(
       [ 
        'status'=>'Failed'
       ]
    );
    $success= DB::table('refund')->insert([
        'date'=>$completeYear,
        'time'=>$time,
        'transyear'=>$monthName,
        'orderid'=>$request->ordernum,
        'user_id'=>$request->userid,
        'restid'=>$request->restid, 
        'amount'=>$amount-($amount*(10/100)),
        'description'=>'Pending',
        'status'=>'Init',
    ]);
   return '10% of your payment for this order will be removed';
 }
 else if (Carbon::parse($order[0]->pickuptime)->diffInMinutes($date->format('G:i'))>=30) {
    DB::table('order')->where('ordernum', $request->ordernum)->update(
       [ 
        'status'=>'Failed'
       ]
    );
    DB::table('transaction')->where('orderid', $request->ordernum)->update(
       [ 
        'status'=>'Failed'
       ]
    );
   return 'You have cancelled your ongoing booking after 30 minutes';
 }
}
else if($order[0]->status=='Pending'){
return 'We would refund you soon';
}
}

//user logout
public function user_logout(Request $request){
    $date=Carbon::now();
    $logout=DB::table('customers')
        ->where('id', $request->id)
        ->update([
            'lasttime'=>$date->format('G:ia').','.$date->format('d/m/Y'),
            'lastdate'=>$date->format('d/m/Y'),
            'lastseen'=>$date->format('G:ia').','.$date->format('d/m/Y'),
            'available'=>'Offline',
        ]);
        if ($logout) {
           return (['message', 'You are logged out']);
        }
}



//delete account
public function user_delete_account(Request $request){
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $rider_name=DB::table('customers')->where('id', $request->id)->get('name');
    $success=DB::table('customers')->where('id', $request->id)->delete();
    if($success){
         DB::table('activity')->insert([
                    'restid' => '',
                    'what' => $time . ', ' . $completeYear . ': ' . $rider_name[0]->name . ' Deleted his Rider account',
                ]);
        DB::table('userdeleteaccount')->insert([
            'account_id'=>$request->id,
            'account_name'=>$rider_name[0]->name,
            'why'=>$request->why,
            'date'=>$time.','.$completeYear
            ]);
        return 'You deleted your account';
    }else{
        return 'Something went wrong';
    }
    
}


}







