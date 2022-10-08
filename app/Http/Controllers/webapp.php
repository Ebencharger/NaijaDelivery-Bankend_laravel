<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Mail\message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Salman\GeoCode\Services\GeoCode;
use App\Payment;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use \KMLaravel\GeographicalCalculator\Facade\GeoFacade;

class webapp extends Controller
{
    //
    //get all rider details
    public function rider_details(){
        $rider = DB::table('rider')->get();
        $riderUser = [];
        for ($i = 0; $i < count($rider); $i++) {
            $rider[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $rider[$i]->picture;
            $riderUser[] = $rider[$i];
        }
        return $riderUser;
    }

    //this is where contactus data are sent to database
    public function contact_us(Request $request){
        $date = Carbon::now();
        $time = $date->format('G:ia');
        $completeYear = $date->format('d/m/Y');
        DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': ' . $request->email . ', contacted us',
        ]);
        $result = DB::table('contactus')->insert([
            'user_name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $message1 = 'Dear Admin' . ',' . '<br>' . 'This is to notify you that we got this feedback from' . $request->name . '<br><br>' . 'The message reads' . $request->message . '<br>' . 'Regards';
        $message = 'Dear ' . $request->name . ',' . '<br>' . 'Thank you for your message.' . '<br><br>' . 'This is to confirm that we have received your enquiry and we will be endeavour to respond within 48 hours. If this is urgent please contact us on 09066936223 or send email:admin@9jadelivery.com' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
        $data1 = ['name' => 'Admin', 'subject' => 'FEEDBACK FROM MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message1];
        $data = ['name' => $request->name, 'subject' => 'NO RESPONSE REPLY', 'view' => 'alert', 'message' => $message];
        $r1 = new message($data1);
        $r = new message($data);
        Mail::to('admin@9jadelivery.com')->send($r1);
        Mail::to($request->email)->send($r);
        if ($result) {
            return 'success';
        } else {
            return 'error';
        }
    }

    //restaurant data are sent to database
    public function restaurant(Request $request){
        $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . $request->businessName . ',registered their Restaurant',
    ]);
    $result = DB::table('restuarant')->insert([
        'name' => $request->businessName,
        'location' => $request->location,
        'phone' => $request->phoneNo,
        'email' => $request->email,
        'instagram' => $request->instagram,
        'regulation' => $request->nafdac,
        'package' => $request->myPackage,
        'currency' => $request->currency,
        'picture' => '',
        'status' => 'Pending',
        'transyear' => $monthName,
        'visible' => $request->password,
        //'password' => Hash::make($request->password),
        'password' => $request->password,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $message1 = 'Dear Admin' . ',' . '<br>' . 'This is to notify you that there is a new Restaurant registered' . $request->businessName . '<br><br>' . '<br>' . 'Regards';
    $message = 'Dear ' . $request->businessName . ',' . '<br>' . 'Thank you for registering with us.' . '<br><br>' . 'This is to confirm that we have received your details and we will be endeavour to respond within 48 hours. To reach our desk urgently please contact us on 09066936223 or send email:admin@9jadelivery.com' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
    $data1 = ['name' => 'Admin', 'subject' => 'FEEDBACK FROM MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message1];
    $data = ['name' => $request->name, 'subject' => 'NO RESPONSE REPLY', 'view' => 'alert', 'message' => $message];
    $r1 = new message($data1);
    $r = new message($data);
    Mail::to('admin@9jadelivery.com')->send($r1);
    Mail::to($request->email)->send($r);
    if ($result) {
        return 'success';
    } else {
        return 'error';
    }
    }

//Get Restuarant data
    public function get_restaurant(){
        $rest = DB::table('restuarant')->get();
        for ($i = 0; $i < count($rest); $i++) {
            $rest[$i]->wallpaper = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $rest[$i]->wallpaper;
            $rest[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $rest[$i]->picture;
            $restuarant[] = $rest[$i];
        }
        return $restuarant;
    }
    
//DELETE Restuarant
    public function delete_restaurant(Request $request){
        $restu = DB::table('restuarant')
        ->where('id', $request->id)
        ->get('name');
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': Restaurant ' . $restu . ' was deleted by you, Admin',
    ]);
   $done=DB::table('restuarant')
        ->where('id', $request->id)
        ->delete();
    if($done){
        return $this->get_restaurant();
    }else{
        return 'something went wrong';
    }
    }

    //rider data are sent to database
   public function send_rider(Request $request){
    $token = mt_rand(1000, 9000);
    $date = Carbon::now();
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . $request->businessName . ' registered as a Rider',
    ]);
    $result = DB::table('rider')->insert([
        'name' => $request->businessName,
        'location' => $request->location,
        'phone' => $request->phoneNo,
        'email' => $request->email,
        'password' => '9jadeliveryRider',
        'token' => $token,
        'picture' => 'images/default.jpg',
        'balance' => 0,
        'status' => 'Pending',
        'company' => 'nil',
        'lasttime' => 'nil',
        'lastdate' => 'nil',
        'lastseen' => 'nil',
        'available' => 'nil',
        'delivertime' => 'nil',
        'bio' => 'nil',
        'transactionDate' => $monthName,
        'address' => $request->address,
        'why' => $request->comment,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $message1 = 'Dear Admin' . ',' . '<br>' . 'This is to notify you that a new Rider registered with' . $request->businessName . '<br><br>' . '<br>' . 'Regards';
    $message = 'Dear ' . $request->businessName . ',' . '<br>' . 'Thank you for registering with us.' . '<br><br>' . 'This is to confirm that we have received your details and we will be endeavour to respond within 48 hours. To reach our desk urgently please contact us on 09066936223 or send email:admin@9jadelivery.com' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
    $data1 = ['name' => 'Admin', 'subject' => 'FEEDBACK FROM MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message1];
    $data = ['name' => $request->name, 'subject' => 'NO RESPONSE REPLY', 'view' => 'alert', 'message' => $message];
    $r1 = new message($data1);
    $r = new message($data);
    Mail::to('admin@9jadelivery.com')->send($r1);
    Mail::to($request->email)->send($r);
    if ($result) {
        return 'success';
    } else {
        return 'error';
    }
   }

   //admin credentials
   public function admin_login(){
    $adminName = 'admin@9jadelivery.com';
    $adminCode = 'admin@9jadelivery.com';
    $admin = DB::table('admins')->get();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $arrange = [];
    $myOrder = [];  
    $riderpay=[];
    $riderpay=DB::table('withdrawal')->get();
    $all_rider=DB::table('rider')->get();
    for ($i=0; $i < count($riderpay); $i++) { 
        for ($k=0; $k <count($all_rider); $k++) { 
           if ($riderpay[$i]->rider_id==$all_rider[$k]->id) {
            $riderpay[$i]->name=$all_rider[$k]->name;
           }
        }
    }

    $transaction = DB::table('transaction')->get();
    if (count($transaction) > 0) {
        for ($i = 0; $i < count($transaction); $i++) {
            $transaction = DB::table('transaction')
                ->join('customers', 'transaction.userid', '=', 'customers.id')
                ->get();
            $transaction[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $transaction[$i]->picture;
            $transaction[$i]->productimage = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $transaction[$i]->productimage;
            $arrange[] = $transaction[$i];
        }
    }
    //readjust arrange
    for ($i = 0; $i < count($arrange)-1; $i++) {
        if ($arrange[$i]->date == $arrange[$i + 1]->date && $arrange[$i]->orderid == $arrange[$i + 1]->orderid) {
            $arrange[$i]->amount = $arrange[$i]->amount + $arrange[$i + 1]->amount;
            $arrange[$i]->deliveryfee=$arrange[$i]->deliveryfee + $arrange[$i + 1]->deliveryfee;
            $arrange[$i]->subtotal=$arrange[$i]->subtotal + $arrange[$i + 1]->subtotal;
            $arrange[$i]->items[] = ((object) ['productimage' => $arrange[$i]->productimage, 'productname' => $arrange[$i]->productname, 'price' => $arrange[$i]->price, 'quantity' => $arrange[$i]->quantity]);
            $arrange[$i]->items[] = ((object) ['productimage' => $arrange[$i + 1]->productimage, 'productname' => $arrange[$i + 1]->productname, 'price' => $arrange[$i + 1]->price, 'quantity' => $arrange[$i + 1]->quantity]);
            array_splice($arrange, $i + 1, 1);
        }
    }
    $activity = DB::table('activity')->get();
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'you logged in',
    ]);
    $ord = DB::table('order')->get();
    $order = DB::table('order')->get();
    $users = DB::table('customers')->get();
    if (count($order) > 0 && count($users) > 0) {
        for ($i = 0; $i < count($order); $i++) {
            for ($k=0; $k < count($users); $k++) { 
                if ($order[$i]->customerid==$users[$k]->id) {
                    $order[$i]->name=$users[$k]->name;
                    $order[$i]->address=$users[$k]->address;
                    $order[$i]->picture=$users[$k]->picture;
                    
                }
            }
            $order[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $order[$i]->picture;
            $order[$i]->status = $ord[$i]->status;
            
            $myOrder[] = $order[$i];
        }
    }
    if (count($admin) == 0) {
        $adminSet = Admin::create([
            'username' => $adminName,
            'password' => $adminCode,
        ]);
    } else {
        $picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $admin[0]->picture;
        $admin = [['name' => $admin[0]->name, 'username' => $admin[0]->username, 'password' => $admin[0]->password, 'picture' => $picture]];
        return [['admin' => $admin, 'user' => $arrange, 'order' => $myOrder, 'activity' => $activity, 'riderpay'=>$riderpay]];
    }
   }

   //operational credentials
   public function opera_login(){
    $operationalName = 'operational@9jadelivery.com';
    $operationalCode = 'operational@9jadelivery.com';
    $user = DB::table('users')->get();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    $arrange = [];
    $myOrder = [];
    $operational = DB::table('operational')->get();
    $riderpay=[];
    $riderpay=DB::table('withdrawal')->get();
    $all_rider=DB::table('rider')->get();
    for ($i=0; $i < count($riderpay); $i++) { 
        for ($k=0; $k <count($all_rider); $k++) { 
           if ($riderpay[$i]->rider_id==$all_rider[$k]->id) {
            $riderpay[$k]->name=$all_rider[$k]->name;
           }
        }
    }
    $transaction = DB::table('transaction')->get();
    for ($i = 0; $i < count($transaction); $i++) {
        $transaction = DB::table('transaction')
            ->join('customers', 'transaction.userid', '=', 'customers.id')
            ->get();
        $transaction[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $transaction[$i]->picture;
        $transaction[$i]->productimage = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $transaction[$i]->productimage;
        $arrange[] = $transaction[$i];
    }

    //readjust arrange
    for ($i = 0; $i < count($arrange)-1; $i++) {
        if ($arrange[$i]->date == $arrange[$i + 1]->date && $arrange[$i]->orderid == $arrange[$i + 1]->orderid) {
            $arrange[$i]->amount = $arrange[$i]->amount + $arrange[$i + 1]->amount;
            $arrange[$i]->deliveryfee=$arrange[$i]->deliveryfee + $arrange[$i + 1]->deliveryfee;
            $arrange[$i]->subtotal=$arrange[$i]->subtotal + $arrange[$i + 1]->subtotal;
            $arrange[$i]->items[] = ((object) ['productimage' => $arrange[$i]->productimage, 'productname' => $arrange[$i]->productname, 'price' => $arrange[$i]->price, 'quantity' => $arrange[$i]->quantity]);
            $arrange[$i]->items[] = ((object) ['productimage' => $arrange[$i + 1]->productimage, 'productname' => $arrange[$i + 1]->productname, 'price' => $arrange[$i + 1]->price, 'quantity' => $arrange[$i + 1]->quantity]);
            array_splice($arrange, $i + 1, 1);
        }
    }
    $activity = DB::table('activity')->get();
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Operational logged in',
    ]);

    $ord = DB::table('order')->get();
    $order = DB::table('order')
        ->join('customers', 'order.customerid', '=', 'customers.id')
        ->get();
    $users = DB::table('customers')->get();
    if (count($order) > 0 && count($users) > 0) {
        for ($i = 0; $i < count($order); $i++) {
            $order = DB::table('order')->get();
            for ($k=0; $k < count($users); $k++) { 
                if ($order[$i]->customerid==$users[$k]->id) {
                    $order[$i]->name=$users[$k]->name;
                    $order[$i]->address=$users[$k]->address;
                    $order[$i]->picture=$users[$k]->picture;
                    
                }
            }
            $order[$i]->status = $ord[$i]->status;
            $order[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $order[$i]->picture;
            $myOrder[] = $order[$i];
        }
    }

    if (count($operational) == 0) {
        DB::table('operational')->insert([
            'username' => $operationalName,
            'password' => $operationalCode,
        ]);
    } else {
        $picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $operational[0]->picture;
        $operational = [['name' => $operational[0]->name, 'username' => $operational[0]->username, 'password' => $operational[0]->password, 'picture' => $picture]];
        return [['admin' => $operational, 'user' => $arrange, 'order' => $myOrder, 'activity' => $activity, 'riderpay'=>$riderpay]];
    }
   }

   //change admin picture
   public function admin_picture(Request $request){
    $imageName = mt_rand(1000, 9000);
    $extension = $request->image->extension();
    $image = $imageName . '.' . $extension;
    $myimage = 'images';
    $saveImage = $myimage . '/' . $image;
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'you changed your profile picture',
    ]);
    $path = $request->image->storeAs( $myimage, $image);
    DB::table('admins')->update([
        'picture' => $saveImage,
    ]);
    return $this->admin_login();
   }

   //change operational picture
public function opera_picture(Request $request){
    $file = mt_rand(100000, 900000);
    $path2 = $request->profpic->extension();
    $filename = $file . '.' . $path2;
    $picture = $request->profpic->storeAs('images', $filename);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Operational changed profile picture',
    ]);
    DB::table('operational')->update([
        'picture' => 'images' . $filename,
    ]);
    return $this->opera_login();
}

//edit admin profile
public function admin_profile(Request $request) {
    DB::table('admins')->update([
        'username' => $request->username,
        'password' => $request->password,
        'name' => $request->name,
    ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'you updated your profile',
    ]);
    return $this->admin_login();
}

//edit operational profile
public function opera_profile (Request $request) {
    DB::table('operational')->update([
        'username' => $request->username,
        'password' => $request->password,
        'name' => $request->name,
    ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Operational updated profile',
    ]);
    return $this->opera_login();
} 

//edit select order
public function edit_order(Request $request) {
    $order = json_decode($request->order);
    $succ=DB::table('order')
        ->where([['ordernum', $order[$request->index]->ordernum]])->update([
            'userid'=>$order[$request->index]->userid
        ]);
        DB::table('transaction')
        ->where([['orderid', $order[$request->index]->ordernum]])->update([
            'riderid'=>$order[$request->index]->userid
        ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'you edited order data at index ' . $request->id,
    ]);
    $order = DB::table('order')->get();
    return $order;
}

//delete selected order
public function delete_order(Request $request) {
    DB::table('order')
        ->where('id', $request->id)
        ->delete();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'you deleted order data at index ' . $request->id,
    ]);
    $order = DB::table('order')->get();
    return $order;
}

//get rider history
public function rider_history(Request $request) {
    $history = DB::table('riderhistory')
        ->where('riderid', $request->id)
        ->get();
    $info = DB::table('rider')
        ->where('id', $request->id)
        ->get();
    $info[0]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $info[0]->picture;
    $orderhistory = ['info' => $info, 'history' => $history];
    return $orderhistory;
}

//add rider manually
public function manual_rider(Request $request) {
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Rider ' . $request->name . ' was registered manually by you',
    ]);
    $success=DB::table('rider')->insert([
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'address' => $request->address,
        'status' => $request->status,
        'password' => '9jadeliveryRider',
        'why' => 'nil',
        'transactionDate' => $monthName,
        'location' => 'nil',
         'token'=>'',
         'balance'=>0,
        'picture' => 'nil',
        'company' => 'nil',
        'transactionDate' => 'nil',
        'lasttime' => 'nil',
        'lastdate' => 'nil',
        'lastseen' => 'nil',
        'available' => 'nil',
        'delivertime' => 'nil',
        'bio' => 'nil',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    if($success){
        return $this->rider_details();
    }else{
        return 'something went wrong';
    }
   
}

//edit rider detail manually
public function edit_rider(Request $request) {
    $rider = json_decode($request->riders);
    DB::table('rider')
        ->where('id', $request->id)
        ->update([
            'name' => $rider[$request->index]->name,
            'phone' => $rider[$request->index]->phone,
            'email' => $rider[$request->index]->email,
            'address' => $rider[$request->index]->address,
        ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Rider info with index' . $request->id . ' was edited by you',
    ]);
    $task=[['approve'=>$this->approve_rider($request)], ['approve'=>$this->rider_details()]];
     if($request->status=='Approved'){
       foreach ($task as $key => $proc) {
        return $proc->approve;
       }
     }
    return $this->rider_details();
}

//delete rider manually
public function delete_rider(Request $request) {
    $dele = DB::table('rider')
        ->where('id', $request->id)
        ->get('name');
    DB::table('rider')
        ->where('id', $request->id)
        ->delete();

    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Rider' . $dele . ' was deleted by you',
    ]);
    return $this->rider_details();
}

//get restaurant menu and trending
public function res_menu(Request $request) {
    $menu = DB::table('menu')
        ->where('restid', $request->id)
        ->get();
    $trending = DB::table('trending')
        ->where('restid', $request->id)
        ->get();
    $allMenu = [];
    $menus = [];
    if ($menu != '' && $trending != '') {
        for ($i = 0; $i < count($menu); $i++) {
            $menu[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $menu[$i]->picture;
            $allMenu[] = $menu[$i];
        }
        for ($i = 0; $i < count($trending); $i++) {
            $trending[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $trending[$i]->picture;
            $allTrending[] = $trending[$i];
        }
        $menus = [['menu' => $allMenu, 'trending' => $trending]];
        return $menus;
    }
}

//get all customers details
public function get_customers() {
    $customer = DB::table('customers')->get();
    $customers = [];
    for ($i = 0; $i < count($customer); $i++) {
        $customer[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $customer[$i]->picture;
        $customers[] = $customer[$i];
    }
    return $customers;
}

// a customer history
public function customer_history (Request $request) {
    $history = DB::table('order')
        ->where('customerid', $request->id)
        ->get();
    $info = DB::table('customers')
        ->where('id', $request->id)
        ->get();
    $info[0]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $info[0]->picture;
    $customerhistory = ['info' => $info, 'history' => $history];
    return $customerhistory;
}

//manual customer
public function manual_customer(Request $request) {
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Customer ' . $request->name . ' was added manually by you',
    ]);
    DB::table('customers')->insert([
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'address' => $request->address,
        'password'=>'9jaDelivery',
        'transactionDate' => 'nil',
        'location' => 'nil',
        'picture' => 'nil',
        'transactionDate' => $monthName,
        'lasttime' => 'nil',
        'lastdate' => 'nil',
        'lastseen' => 'nil',
        'available' => 'nil',
        'bio' => 'nil',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return $this->get_customers();
}


//edit customer
public function edit_customer(Request $request) {
    $customers = json_decode($request->customers);
    return $customers[$request->index];
    DB::table('customers')
        ->where('id', $request->id)
        ->update([
            'name' => $customers[$request->index]->name,
            'phone' => $customers[$request->index]->phone,
            'email' => $customers[$request->index]->email,
            'address' => $customers[$request->index]->address,
        ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Customer by' . $request->name . ' index was edited by you',
    ]);
   return $this->get_customers();
}

//delete a customer
public function delete_customer(Request $request) {
    DB::table('customers')
        ->where('id', $request->id)
        ->delete();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Customer with id' . $request->id . ' was removed by you',
    ]);
    return $this->get_customers();
}

//get all foods
public function get_food() {
    $rest = DB::table('restuarant')->get();
    $myFood = DB::table('food')->get();
    $foods = [];
    for ($i = 0; $i < count($myFood); $i++) {
        $myFood[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $myFood[$i]->picture;
        for ($j = 0; $j < count($rest); $j++) {
            if ($rest[$j]->id == $myFood[$i]->restuarant) {
                $myFood[$i]->restname = $rest[$j]->name;
                $myFood[$i]->location = $rest[$j]->location;
                $myFood[$i]->address = $rest[$j]->address;
            }
        }
        $foods[] = $myFood[$i];
    }
    return $foods;
}

//delete a food
public function delete_food(Request $request) {
    DB::table('food')
        ->where('id', $request->id)
        ->delete();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Food with id ' . $request->id . ' was removed by you',
    ]);
    return $this->get_food();
}

//get a selected food details
public function selected_food(Request $request) {
    $myFood = DB::table('food')
        ->where('id', $request->id)
        ->get();
    $foods = [];
    for ($i = 0; $i < count($myFood); $i++) {
        $myFood[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $myFood[$i]->picture;
        $foods[] = $myFood[$i];
    }
    return $foods;
}

//get all coupons

public function get_coupon() {
    $coupon = DB::table('coupon')->get();
    return $coupon;
}

//add new coupon to database
public function new_coupon(Request $request) {
    DB::table('coupon')->insert([
        'coupon_code' => $request->coupon_code,
        'expiry_date' => $request->expiry_date,
        'amount' => $request->price,
        'status' => 'Active',
    ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'new coupon was generated by you',
    ]);
    $coupon = DB::table('coupon')->get();
    return $coupon;
}

//edit a coupon
public function edit_coupon(Request $request) {
    $coupon = json_decode($request->coupon);
    DB::table('coupon')
        ->where('id', $request->id)
        ->update([
            'coupon_code' => $coupon[$request->index]->coupon_code,
            'expiry_date' => $coupon[$request->index]->expiry_date,
            'amount' => $coupon[$request->index]->amount,
        ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Coupon with id ' . $request->id . ' was edited by you',
    ]);
    return $this->get_coupon;
}

//delete coupon
public function delete_coupon(Request $request) {
    DB::table('coupon')
        ->where('id', $request->id)
        ->delete();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'Coupon with id ' . $request->id . ' was removed by you',
    ]);
    return $this->get_coupon();
}

//get refund details
public function get_refund() {
    $fund = DB::table('refund')->get();
    $refund = [];
    for ($i = 0; $i < count($fund); $i++) {
        $fund = DB::table('refund')->get();
        $customer = DB::table('customers')
            ->where('id', $fund[$i]->user_id)
            ->get();
        $fund[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $customer[0]->picture;
        $fund[$i]->name = $customer[0]->name;
        $refund[] = $fund[$i];
    }
    return $refund;
}

//post refund
public function post_refund(Request $request){
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
  $exist=DB::table('refund')->where('orderid', $request->orderid)->get();
  if (count($exist)>=1) {
    return 'This Transaction has been refunded already';
  }else if(count($exist)==0){
    $success= DB::table('refund')->insert([
        'date'=>$completeYear,
        'time'=>$time,
        'transyear'=>$monthName,
        'orderid'=>$request->orderid,
        'user_id'=>$request->userid,
        'restid'=>$request->restid,
        'amount'=>$request->amount,
        'description'=>'Pending',
        'status'=>'Init',
    ]);
    if ($success) {
        return $this->get_refund();
    }
    else{
        return 'Something went wrong';
    }
  }

}

//new refund 
public function new_refund(Request $request) {
    DB::table('refund')->insert([
        'user_id' => $request->user_id,
        'restid' => $request->restid,
        'date' => $request->mydate,
        'time' => $request->mytime,
        'description' => $request->desc,
        'amount' => $request->myamount,
        'transyear' => $request->mytransyear,
        'status' => $request->mestatus,
    ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'You processed a refund for user with id ' . $request->user_id,
    ]);
   return $this->get_refund();
}


//edit refund
public function edit_refund(Request $request) {
    $refund = json_decode($request->refund);
    DB::table('refund')
        ->where('id', $request->id)
        ->update([
            'date' => $refund[$request->index]->date,
            'time' => $refund[$request->index]->time,
            'amount' => $refund[$request->index]->amount,
            'description' => $refund[$request->index]->description,
            'status' => $refund[$request->index]->status,
        ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'You edited a refund process for user with id ' . $request->id,
    ]);
    return $this->get_refund();
}

//delete refund
public function delete_refund(Request $request) {
    DB::table('refund')
        ->where('id', $request->id)
        ->delete();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'You deleted a refund process for user with id ' . $request->user_id,
    ]);
    return $this->get_refund();
}

//get review
public function get_review() {
    $view = DB::table('review')->get();
    $review = [];
    for ($i = 0; $i < count($view); $i++) {
        $customer = DB::table('customers')
            ->where('id', $view[$i]->user_id)
            ->get();
        $view[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $customer[0]->picture;
        $view[$i]->name = $customer[0]->name;
        $review[] = $view[$i];
    }
    return $review;
}

//delete review
public function delete_review(Request $request) {
    DB::table('review')
        ->where('id', $request->id)
        ->delete();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => '',
        'what' => $time . ', ' . $completeYear . ': ' . 'You deleted a review for user with id ' . $request->id,
    ]);
   return $this->get_review();
}

//approve rider
public function approve_rider(Request $request) {
    $email=DB::table('rider')
    ->where('id', $request->id)->get('email');
    DB::table('rider')
        ->where('id', $request->id)
        ->update([
            'status' => 'Approved',
        ]);
        $message = 'Dear Rider' . ',' . '<br>' . 'This is to notify you that your account has been approved. Thank you for registering with us.' . '<br><br>' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
        $data1 = ['name' => 'Admin', 'subject' => 'FEEDBACK FROM MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message];
        $r1 = new message($data1);
        Mail::to($email[0]->email)->send($r1);
        return $this->admin_login();
}

//approve restaurant
public function approve_restaurant(Request $request) {
    $email=DB::table('restuarant')
    ->where('id', $request->id)->get('email');
    DB::table('restuarant')
        ->where('id', $request->id)
        ->update([
            'status' => 'Approved',
        ]);
        $message = 'Dear Partner' . ',' . '<br>' . 'This is to notify you that your account has been approved. Thank you for registering with us.' . '<br><br>' . '<br><br>' . 'Thank you' . '<br>' . 'Regards,' . '<br><br>' . 'Operations team';
        $data1 = ['name' => 'Admin', 'subject' => 'FEEDBACK FROM MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message];
        $r1 = new message($data1);
        Mail::to($email[0]->email)->send($r1);
        return $this->admin_login();
}


//pay rider
public function pay_rider(Request $request){
$paid=DB::table('withdrawal')->where('id', $request->id)->update([
    'status'=>'Paid'
]);
if ($paid) {
   return $this->admin_login();
}
}


//get data for restaurant dashboard
public  function rest_dashboard(Request $request) {
    $my_detail = [];
    $ind = $request->index;
    $my_menu = [];
    $allOrder = [];
    $food = [];
    $transaction = [];
    $customer_map = [];
    $months = ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'November', 'December'];
    $my_revenue = [];
    $customer = [];
    $myRes = json_decode(
        DB::table('restuarant')
            ->where('id', $ind)
            ->get('name'),
    );
    $myRes = $myRes[0]->name;
    $year = $request->year;
    //get all restaurant activities
    $activity = DB::table('activity')
        ->where('restid', $ind)
        ->get();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => $ind,
        'what' => $time . ', ' . $completeYear . ': ' . $myRes . '-Admin logged in',
    ]);
    // $year='2022';
    $total_customer = 0;
    $my_order = [];
    $offer = [];
    $order = 0;
    $check = 0;
    $newOrder = [];
    $revenue = 0;
    $menu = 0;
    $my_customer = [];
    $myself = [];
    $review = [];
    //how to compare string
    // $check= Str::contains($year, '2022');
    $detail = DB::table('restuarant')
        ->where('id', $ind)
        ->get();
    $trans = DB::table('transaction')
        ->where('restid', $ind)
        ->join('customers', 'transaction.userid', '=', 'customers.id')
        ->get();

    $review = DB::table('review')
        ->where('restid', $ind)
        ->get();

    $food = DB::table('food')
        ->where('restuarant', $ind)
        ->get();
    $allOrder = DB::table('order')
        ->where('restid', $ind)
        ->join('customers', 'order.customerid', '=', 'customers.id')
        ->get();
    $my_menu = DB::table('menu')
        ->where([['restid', $ind], ['year', $year]])
        ->get();
    $my_revenue = DB::table('revenue')
        ->where([['rest_id', $ind], ['year', $year]])
        ->get();
    $my_order = DB::table('order')
        ->where([['restid', $ind], ['year', $year]])
        ->get();
    $offer = DB::table('offer')
        ->where('restid', $ind)
        ->get();

    //convert picture to storage for transaction and order and join rider.
    $rider = DB::table('rider')->get();
    for ($i = 0; $i < count($trans); $i++) {
        $trans[$i]->productimage = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $trans[$i]->productimage;
        $trans[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $trans[$i]->picture;
        for ($k = 0; $k < count($rider); $k++) {
            if ($rider[$k]->id == $trans[$i]->riderid && $k == $i) {
                $rider[$k]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $rider[$k]->picture;
                $trans[$i]->rider = $rider[$k];
            }
        }
        $transaction[] = $trans[$i];
    }

    //convert offer images for storage
    for ($i = 0; $i < count($offer); $i++) {
        $offer[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $offer[$i]->picture;
    }

    //convert food images for storage
    for ($i = 0; $i < count($food); $i++) {
        $food[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $food[$i]->picture;
    }

    //readjust transaction and order for the sake of multiple transation and order
    //if data duplicated, resort them
    for ($i = 0; $i < count($transaction); $i++) {
        if ($transaction[$i]->date == $transaction[$i + 1]->date && $transaction[$i]->orderid == $transaction[$i + 1]->orderid) {
            $transaction[$i]->amount = $transaction[$i]->amount + $transaction[$i + 1]->amount;
            $transaction[$i]->items[] = ((object) ['productimage' => $transaction[$i]->productimage, 'productname' => $transaction[$i]->productname, 'price' => $transaction[$i]->price, 'quantity' => $transaction[$i]->quantity, 'subtotal' => $transaction[$i]->subtotal, 'deliveryfee' => $transaction[$i]->deliveryfee, 'discount' => $transaction[$i]->discount]);

            $transaction[$i]->items[] = ((object) ['productimage' => $transaction[$i + 1]->productimage, 'productname' => $transaction[$i + 1]->productname, 'price' => $transaction[$i + 1]->price, 'quantity' => $transaction[$i + 1]->quantity, 'subtotal' => $transaction[$i + 1]->subtotal, 'deliveryfee' => $transaction[$i + 1]->deliveryfee, 'discount' => $transaction[$i + 1]->discount]);
            array_splice($transaction, $i + 1, 1);
        }
    }

    //check how many customer each restaurant has
    for ($i = 0; $i < count($my_order) - 1; $i++) {
        if (count($my_order) > 1) {
            for ($j = $i; $j < count($my_order) - 1; $j++) {
                if ($my_order[$i + 1]->customerid != $my_order[$j + 1]->customerid) {
                    if (count($my_order) != count($my_order)) {
                        $customer[] = $my_order[$j + 1];
                    }
                } elseif ($my_order[$i + 1]->customerid == $my_order[$j + 1]->customerid) {
                    $customer[$i] = $my_order[$j + 1];
                }
            }
        } elseif (count($my_order) == 1) {
            $customer[] = $my_order[$i];
        }
    }

    //construct revenue map
    for ($i = 0; $i < count($months); $i++) {
        for ($j = 0; $j < count($customer); $j++) {
            if (Str::contains($customer[$j]->transactionDate, $months[$i]) == 1) {
                $customer_map[] = ((object) ['month' => $months[$i], 'cust' => 1]);
            } else {
                $customer_map[] = ((object) ['month' => $months[$i], 'cust' => 0]);
            }
        }
    }
    //if data duplicated, resort them
    for ($i = 0; $i < count($customer_map); $i++) {
        if ($customer_map[$i] == $customer_map[$i + 1]) {
            $customer_map[$i]->cust = $customer_map[$i]->cust + $customer_map[$i + 1]->cust;
            array_splice($customer_map, $i + 1, 1);
        }
    }

    //prepare revenue
    if (count($my_revenue) > 0) {
        for ($i = 0; $i < count($my_revenue); $i++) {
            $revenue = $revenue + (int) $my_revenue[$i]->amount;
            $my_revenue[$i]->map[] = $my_revenue[$i]->amount / 1000;
        }
    }

    if (count($my_revenue) != 0) {
        for ($i = 0; $i == 0; $i++) {
            for ($j = 0; $j < 12; $j++) {
                if (count($my_revenue[0]->map) != 12) {
                    $my_revenue[0]->map[] = 0;
                }
            }
        }
    }

    for ($i = 0; $i < count($my_revenue) - 1; $i++) {
        $my_revenue[0]->map[$i + 1] = $my_revenue[$i + 1]->map[0];
    }

    if (count($my_revenue) == 0) {
        $my_revenue = $my_revenue->push((object) ['map' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]]);
    }

    //refund
    $fund = DB::table('refund')
        ->where('restid', $ind)
        ->get();
    $refund = [];
    for ($i = 0; $i < count($fund); $i++) {
        $customer = DB::table('customers')
            ->where('id', $fund[$i]->user_id)
            ->get();
        $fund[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $customer[0]->picture;
        $fund[$i]->name = $customer[0]->name;
        $refund[] = $fund[$i];
    }
    $revenue = $revenue / 1000;
    $menu = count($my_menu);
    $order = count($my_order);
    $total_customer = count($customer);
    $myself = [['my_revenue' => $my_revenue, 'menu' => $menu, 'order' => $order, 'total_customer' => $total_customer, 'revenue' => $revenue, 'customer_map' => $customer_map, 'transaction' => $transaction, 'food' => $food, 'review' => $review, 'offer' => $offer, 'activity' => $activity, 'refund' => $refund]];
    return $myself;
}

//delete a restaurant
public function account_delete(Request $request) {
    $address = $request->address;
    DB::table($address)
        ->where('id', $request->account_id)
        ->delete();
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => $request->account_id,
        'what' => $time . ', ' . $completeYear . ': ' . $request->account_name . '-Admin deleted their account because' . $request->why,
    ]);
    DB::table('account_delete')->insert([
        'account_id' => $request->account_id,
        'account_name' => $request->account_name,
        'why' => $request->why,
        'created_at' => now(),
    ]);
    return 'success';
}

//update a restaurant
public function update_restaurant(Request $request) {
    $imageName = mt_rand(1000, 9000);
    $imageName2 = mt_rand(1000, 9000);
    $extension = $request->admin_img->extension();
    $admin_img = $imageName . '.' . $extension;
    $myimage = 'images';
    $restuarant = [];
    $saveImage = $myimage . '/' . $admin_img;
    $path = $request->admin_img->storeAs($myimage, $admin_img);
    $extension2 = $request->wallpaper->extension();
    $wallpaper = $imageName2 . '.' . $extension2;
    $myimage2 = 'images';
    $saveImage2 = $myimage2 . '/' . $wallpaper;
    $path = $request->wallpaper->storeAs($myimage2, $wallpaper);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => $request->id,
        'what' => $time . ', ' . $completeYear . ': ' . $request->name . ' with id' . $request->id . '-updated their profile',
    ]);
    $update = DB::table('restuarant')
        ->where('id', $request->id)
        ->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'location' => $request->location,
            'admin' => $request->admin_name,
            'picture' => $saveImage,
            'wallpaper' => $saveImage2,
            'motto' => $request->motto,
            'address' => $request->address,
        ]);
    if ($update) {
        $rest = DB::table('restuarant')->get();
        for ($i = 0; $i < count($rest); $i++) {
            $rest[$i]->wallpaper = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $rest[$i]->wallpaper;
            $rest[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $rest[$i]->picture;
            $restuarant[] = $rest[$i];
        }
        return $restuarant;
    } else {
        return '500';
    }
}

//upload item
public function upload_item(Request $request) {
    $date = Carbon::now();
    $orderNum = '#' . mt_rand(100000, 900000);
    $transYear = $date->format('F') . ' ' . $date->format('Y');
    $allValue = json_decode($request->value);
    $imageName = mt_rand(1000, 9000);
    $extension = $request->image->extension();
    $image = $imageName . '.' . $extension;
    $myimage = 'images';
    $saveImage = $myimage . '/' . $image;
    $path = $request->image->storeAs($myimage, $image);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => $allValue->id,
        'what' => $time . ', ' . $completeYear . ': Restaurant with id ' . $allValue->id . ' - upload a food item',
    ]);
    $upload = DB::table('food')->insert([
        'restuarant' => $allValue->id,
        'item_name' => $allValue->item_name,
        'category' => $allValue->category,
        'what' => $allValue->what,
        'add_ons' => $allValue->add_ons,
        'price' => $allValue->price,
        'picture' => $saveImage,
        'availablilty' => $allValue->availability,
        'discount' => $allValue->discountt,
        'details' => $allValue->details,
        'transyear' => $transYear,
        'item_id' => $orderNum,
        'total_order' => 0,
        'favourite' => 0,
    ]);
    DB::table('menu')->insert([
        'restid' => $allValue->id,
        'year' => $date->format('Y'),
        'picture' => $saveImage,
    ]);
    if ($upload) {
        return 'yes';
    } else {
        return 'no';
    }
}

//update restaurant
public function update_rest_food(Request $request) {
    $date = Carbon::now();
    $transYear = $date->format('F') . ' ' . $date->format('Y');
    $allValue = json_decode($request->value);
    if (Str::contains($request->image, 'wamp') || Str::contains($request->image, 'tmp')) {
        $imageName = mt_rand(1000, 9000);
        $extension = $request->image->extension();
        $image = $imageName . '.' . $extension;
        $myimage = 'images';
        $saveImage = $myimage . '/' . $image;
        $path = $request->image->storeAs($myimage, $image);
    } else {
        $saveImage = $request->image;
    }

    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => $request->id,
        'what' => $time . ', ' . $completeYear . ': Restaurant with id ' . $request->id . ' - edited a food item-details',
    ]);

    $upload = DB::table('food')->update([
        'restuarant' => $request->id,
        'item_name' => $allValue->item_name,
        'category' => $allValue->category,
        'what' => $allValue->what,
        'add_ons' => $allValue->add_ons,
        'price' => $allValue->price,
        'picture' => $saveImage,
        'availablilty' => $allValue->availability,
        'discount' => $allValue->discountt,
        'details' => $allValue->details,
        'transyear' => $transYear,
        'item_id' => $request->orderNum,
        'total_order' => $request->total_order,
        'favourite' => $request->favourite,
    ]);
    DB::table('menu')->update([
        'restid' => $request->restid,
        'year' => $transYear,
        'picture' => $saveImage,
    ]);
    if ($upload) {
        return 'yes';
    } else {
        return 'no';
    }
}

//delete restaurant food
public function rest_delete_food(Request $request) {
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => $request->id,
        'what' => $time . ', ' . $completeYear . ': Restaurant with id' . $request->id . '-deleted a food item',
    ]);
    $res = DB::table('food')
        ->where('id', $request->id)
        ->delete();
    if ($res) {
        return 'yes';
    }
}

//upload offer
public function upload_offer(Request $request) {
    $done = DB::table('offer')->insert([
        'restid' => $request->restid,
        'name' => $request->name,
        'category' => $request->category,
        'what' => $request->what,
        'free_package' => $request->free_package,
        'meat' => $request->meat,
        'drink' => $request->drink,
        'picture' => $request->image,
        'amount' => $request->amount,
        'reviews' => 0,
    ]);
    if ($done) {
        return 'yes';
    }
}

//delete offer from a restaurant
public function rest_delete_offer(Request $request) {
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => $request->id,
        'what' => $time . ', ' . $completeYear . ': Restaurant with id' . $request->id . ' deleted an offer',
    ]);
    $res = DB::table('offer')
        ->where('id', $request->id)
        ->delete();
    if ($res) {
        return 'yes';
    }
}

//update restaurant offer
public function update_rest_offer(Request $request) {
    $upload = DB::table('offer')->update([
        'name' => $request->name,
        'drink' => $request->drink,
        'meat' => $request->meat,
        'free_package' => $request->free_package,
        'amount' => $request->amount,
        'restid' => $request->restid,
    ]);
    $date = Carbon::now();
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $monthName = $date->format('F') . ' ' . $date->format('Y');
    DB::table('activity')->insert([
        'restid' => $request->restid,
        'what' => $time . ', ' . $completeYear . ': Restaurant with id' . $request->restid . '-updated an offer-details',
    ]);
    if ($upload) {
        return 'yes';
    } else {
        return 'no';
    }
}

//mainpage order and delivery
//offer
public function offer(Request $request) {
    $off = DB::table('offer')
        ->where('restid', $request->index)
        ->get();
    $offer = [];
    if (count($off) > 0) {
        for ($i = 0; $i < count($off); $i++) {
            $off[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $off[$i]->picture;
            $offer[] = $off[$i];
        }
    }
    return $offer;
}

//restaurant food
public function rest_food(Request $request) {
    $rest = DB::table('restuarant')->get();
    $fod = DB::table('food')
        ->where('restuarant', $request->index)
        ->get();
    $food = [];
    if (count($fod) > 0) {
        for ($i = 0; $i < count($fod); $i++) {
            $fod[$i]->picture = 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/' . $fod[$i]->picture;
            for ($j = 0; $j < count($rest); $j++) {
                if ($rest[$j]->id == $fod[$i]->restuarant) {
                    $fod[$i]->restname = $rest[$j]->name;
                    $fod[$i]->location = $rest[$j]->location;
                    $fod[$i]->address = $rest[$j]->address;
                }
            }
            $food[] = $fod[$i];
        }
    }
    return $food;
}

//if customer has details, just input his or her order in database
public function order_one (Request $request) {
    $date = Carbon::now();
    $year = $date->format('Y');
    $orderNum = '#' . mt_rand(100000, 900000);
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $transYear = $date->format('F') . ' ' . $date->format('Y');
    $customers = DB::table('customers')->get();
    if ($request->where == 'order') {
        $date = Carbon::now();
        $time = $date->format('G:ia');
        $completeYear = $date->format('d/m/Y');
        $monthName = $date->format('F') . ' ' . $date->format('Y');
        DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': A user with id-' . $request->id . ' just placed an order',
        ]);
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
                'userid' => 'nil',
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
                'deliverto' => $request->address,
                'delivertime' => $request->$time,
                'transactionDate' => $transYear,
            ]);
            DB::table('transaction')->insert([
                'userid' => $request->id,
                'restid' => $restid,
                'riderid' => '',
                'productname' => $productType,
                'productimage' => $image,
                'price' => $serviceFee,
                'quantity' => 1,
                'riderarrive'=>'',
                'pickup'=>'',
                'orderid' => $orderNum,
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
        $message1 = 'This is to notify you that a customer just ordered for' . $request->products->item_selected->item_name . 'and we need one of our riders to reach out as soon as possible. Check Dashboard for more details. This is the customer details:-' . $request->address . ', ' . $request->phone . ', ' . $request->city . ', ' . $request->country;
        $data1 = ['name' => '9jadelivery', 'subject' => 'MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message1];
        $r1 = new message($data1);
        Mail::to('admin@9jadelivery.com')->send($r1);
    } elseif ($request->where == 'delivery') {
        $date = Carbon::now();
        $time = $date->format('G:ia');
        $completeYear = $date->format('d/m/Y');
        $monthName = $date->format('F') . ' ' . $date->format('Y');
        DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': A user with id-' . $request->id . ' wants to deliver a product',
        ]);
        DB::table('order')->insert([
            'userid' => 'nil',
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
            'pickuptime' => $time,
            'Status' => 'Pending',
            'deliverto' => $request->receiverAddress . ',' . $request->receiverCountry . ',' . $request->receiverPhone,
            'delivertime' => $request->$time,
            'transactionDate' => $transYear,
        ]);
        DB::table('transaction')->insert([
            'userid' => $request->id,
            'restid' => '0',
            'riderid' => '',
            'productname' => $request->packType,
            'productimage' => '',
            'price' => '',
            'quantity' => '',
            'orderid' => $orderNum,
            'riderarrive'=>'',
            'pickup'=>'',
            'amount' => '',
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
    }
}

//if customer has no info yet
public function order_two(Request $request) {
    $date = Carbon::now();
    $year = $date->format('Y');
    $orderNum = '#' . mt_rand(100000, 900000);
    $time = $date->format('G:ia');
    $completeYear = $date->format('d/m/Y');
    $transYear = $date->format('F') . ' ' . $date->format('Y');
    $customers = DB::table('customers')->get();
    $count=count($customers);
    $product = json_decode($request->products);
    if ($request->where == 'order') {
        $date = Carbon::now();
        $time = $date->format('G:ia');
        $completeYear = $date->format('d/m/Y');
        $monthName = $date->format('F') . ' ' . $date->format('Y');
        DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': A user with' . ($request->id + 1) . ' just placed an order',
        ]);
        DB::table('customers')->insert([
            'id' => $customers[$count-1]->id + 1,
            'name' => $request->name,
            'location' => $request->country,
            'email' => $request->email,
            'password' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
            'picture' => 'nil',
            'status' => 'Pending',
            'transactionDate' => $transYear,
            'lasttime' => $time,
            'lastdate' => $completeYear,
            'lastseen' => $time,
            'available' => 'Offline',
            'bio' => 'nil',
        ]);
        foreach ($product as $key => $value) {
            $serviceFee = json_decode($value->addition->total_amount);
            $productType = $value->item_selected->item_name;
            $image='';
            $pickup = $value->pickup;
            $restid = $value->restid;
            $image='';
            $count=strlen($value->item_selected->picture);
       if (Str::contains($value->item_selected->picture, 'https://www.naijadelivery.9jadelivery.com/NaijaDelivery/storage/app/')) {
            $image=substr($value->item_selected->picture, 68, $count);
             }
            DB::table('order')->insert([
                'userid' => 'nil',
                'customerid' => $request->id + 1,
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
                'deliverto' => $request->address,
                'delivertime' => $request->$time,
                'transactionDate' => $transYear,
            ]);

            DB::table('transaction')->insert([
                'userid' => $request->id,
                'restid' => $restid,
                'riderid' => '',
                'productname' => $productType,
                'productimage' => $image,
                'price' => $serviceFee,
                'quantity' => 1,
                'orderid' => $orderNum,
                'amount' => $serviceFee,
                'riderarrive'=>'',
                'pickup'=>'',
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
        $message1 = 'This is to notify you that a customer just ordered for'. 'and we need one of our riders to reach out as soon as possible. Check Dashboard for more details. This is the customer details:-' . $request->address . ', ' . $request->phone . ', ' . $request->city . ', ' . $request->country;
        $data1 = ['name' => '9jadelivery', 'subject' => 'MONITORING AND CONTROL SYSTEM', 'view' => 'alert', 'message' => $message1];
        $r1 = new message($data1);
        Mail::to('admin@9jadelivery.com')->send($r1);
    } elseif ($request->where == 'delivery') {
        $date = Carbon::now();
        $time = $date->format('G:ia');
        $completeYear = $date->format('d/m/Y');
        $monthName = $date->format('F') . ' ' . $date->format('Y');
        DB::table('activity')->insert([
            'restid' => '',
            'what' => $time . ', ' . $completeYear . ': A user with' . ($request->id + 1) . ' wants us to deliver a product',
        ]);
        DB::table('customers')->insert([
            'id' => $customers[$count-1]->id + 1,
            'name' => $request->senderName,
            'location' => $request->senderCountry,
            'email' => $request->senderName,
            'password' => $request->senderName,
            'address' => $request->senderAddress,
            'phone' => $request->senderPhone,
            'picture' => 'nil',
            'status' => 'Pending',
            'transactionDate' => $transYear,
            'lasttime' => $time,
            'lastdate' => $completeYear,
            'lastseen' => $time,
            'available' => 'Offline',
            'bio' => 'nil',
        ]);
        DB::table('order')->insert([
            'userid' => 'nil',
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
            'pickuptime' => $time,
            'Status' => 'Pending',
            'deliverto' => $request->receiverAddress . ',' . $request->receiverCountry . ',' . $request->receiverPhone,
            'delivertime' => $request->$time,
            'transactionDate' => $transYear,
        ]);

        DB::table('transaction')->insert([
            'userid' => $request->id,
            'restid' => '0',
            'riderid' => '',
            'productname' => $request->packType,
            'productimage' => '',
            'price' => '',
            'quantity' => '',
            'orderid' => $orderNum,
            'riderarrive'=>'',
            'pickup'=>'',
            'amount' => '',
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
    }
}


//loyalty point
public function loyalty_point(Request $request){
   $customer=DB::table('customers')->get();
   $couponCode=mt_rand(1000000, 9000000);
   $each=[];
   $amount=0;
   $min=50000;
   foreach ($customer as $key => $value) {
    $each=DB::table('order')->where('customerid', $value->id)->get();
    $coupon=DB::table('coupon')->where('customerid',$value->id)->get();
    foreach ($each as $key => $valu) {
        $amount=$amount+$valu->servicefee;
    } 
    for ($j=count($coupon); $j <XSD_POSITIVEINTEGER ; $j++) { 
    if(count($coupon)>=0 && ($amount>=$min*($j+1))){
        DB::table('coupon')->insert([
            'coupon_code'=>$couponCode,
            'expiry_date'=>'Not now',
            'amount'=>1500,
            'customerid'=>$value->id,
            'status'=>'Active',
            'created_at'=>now(),
            'updated_at'=>now()
            ]);
   $message = 'This is to notify you that you have won our Loyalty points and here is your coupon code: -'.'<h1>'.$couponCode.'</h1>'.';to be used anytime you feel like.'. 'This code can only be used once';
           $data = ['name' => $value->name, 'subject' => 'NO REPLY RESPONSE', 'view' => 'alert', 'message' => $message];
           $r1 = new message($data);
           Mail::to($value->email)->send($r1);
           return 'Loyalty Point of 1500 and your coupon code is'.': '.$couponCode;
    }
else if(count($coupon)>=0 && ($amount<=$min*($j+1))){
return 'i can not have it this time';                
}
    }
}
}
      
public function is_rider_available(Request $request){
 $dst=0;
 $see=[];
 $holding=DB::table('holding')->get();
 $almigh=[];
 $all_holding=[];
$rider=DB::table('rider')->get();
$order=DB::table('order')->get();
$amount=[];
$card_no=[];
$ccExpiryMonth=[];
$ccExpiryYear=[];
$cvvNumber=[];
foreach ($holding as $key => $va) {
$all_holding=DB::table('order')->where([['customerid', $va->userid], ['status', 'Pending'], ['userid', '']])->get();
}
    

try {
    if(count($all_holding)>0){
        foreach ($all_holding as $key => $value) {
            $amount=DB::table('holding')->where([['userid', $value->customerid], ['ordernum', $value->ordernum]])->get('amount');
         
         $card_no=DB::table('holding')->where([['userid', $value->customerid], ['ordernum', $value->ordernum]])->get('card_no');
        
         $ccExpiryMonth=DB::table('holding')->where([['userid', $value->customerid], ['ordernum', $value->ordernum]])->get('ccExpiryMonth');
        
         $ccExpiryYear=DB::table('holding')->where([['userid', $value->customerid], ['ordernum', $value->ordernum]])->get('ccExpiryYear');
        
         $cvvNumber=DB::table('holding')->where([['userid', $value->customerid], ['ordernum', $value->ordernum]])->get('cvvNumber');
        
         for ($i=0; $i < count($rider); $i++) { 
            $address = $value->pickup;
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
            foreach ($a as $key => $val) {
              $valu[]= $val;
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
                       
                        $stripe = new \Stripe\StripeClient(
                            'sk_test_51LCpCjApcBBpI4w28zyYpM8wjMNnC6QZ6ZMtIgbFX3a9VUPb0ebuOLqW0y2sWM3FeiDJOJ5BSsInoibggme72IpO00qKs68o7n'
                          );
                     try {
                        $token=$stripe->tokens->create([
                            'card' => [
                                "number"    => $card_no[0]->card_no,
                                "exp_month" => $ccExpiryMonth[0]->ccExpiryMonth,
                                "exp_year"  => $ccExpiryYear[0]->ccExpiryYear,
                                "cvc"       => $cvvNumber[0]->cvvNumber,
                            ],
                          ]);
                    if (!isset($token['id'])) {
                     return 'no id';
                     }
                     $charge = $stripe->charges->create([
                     'card' => $token['id'],
                     'currency' => 'USD',
                     'amount' => $amount[0]->amount*100,
                     'description' => 'wallet',
                     ]);
                     if($charge['status'] == 'succeeded') {
                        DB::table('transaction')->where([['userid', $value->customerid],['status', 'Pending'], ['orderid', $value->ordernum]])->update([
                            'riderid'=>$rider[$i]->id
                         ]);
                        $touch=DB::table('order')->where([['customerid', $value->customerid],['status', 'Pending'], ['ordernum', $value->ordernum]])->update([
                            'userid'=>$rider[$i]->id
                         ]);
                        DB::table('holding')->where([['userid', $value->customerid], ['ordernum', $value->ordernum]])->delete();
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
              else if (($i==count($saa)-1) && ($valu[$i]->{'km'}>=6)){
                  return 'No Rider is available yet';
              }
            }
             return $holding;
        }
    }
    else{
        return 'No holding available';
    }
} catch (\Throwable $th) {
    return 'No holding available';
}
}

//revisit rider_cancel
public function revisit_cancel_order(Request $request){
    $dst=0;
    $see=[];
    $almigh=[];
   $rider=DB::table('rider')->get();
   $order=DB::table('order')->where('userid','')->get();
   try {
       if(count($order)>0){
           foreach ($order as $key => $value) {
            for ($i=0; $i < count($rider); $i++) { 
               $address = $value->pickup;
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
               foreach ($a as $key => $val) {
                 $valu[]= $val;
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
                        try {
                           DB::table('transaction')->where([['userid', $value->customerid],['status', 'Pending'], ['orderid', $value->ordernum]])->update([
                               'riderid'=>$rider[$i]->id
                            ]);
                           $touch=DB::table('order')->where([['customerid', $value->customerid],['status', 'Pending'], ['ordernum', $value->ordernum]])->update([
                               'userid'=>$rider[$i]->id
                            ]);
                        return 'I got a new rider for the task';
                        }  catch (Exception $e) {
                        return (['error',$e->getMessage()]);
                        } catch(\Stripe\Exception\CardException $e) {
                        return (['error',$e->getMessage()]);
                        } catch(\Stripe\Exception\InvalidArgumentException $e) {
                        return (['error',$e->getMessage()]);
                        }
           
                         }
                     }
                 }
                 else if (($i==count($saa)-1) && ($valu[$i]->{'km'}>=6)){
                     return 'No Rider is available yet';
                 }
               }
           }
       }
       else{
           return 'No Cancelled Delivery available';
       }
   } catch (\Throwable $th) {
       return 'No Cancelled Delivery available';
   }
   }


}
