<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController as RegisterController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailResetPasswordNotification;
use Illuminate\Support\Facades\Password;

class ApiRegisterController extends RegisterController
{
    use RegistersUsers;

    public function createCustomer(Request $request){
        $request->request->add(['user_type_id' => 5]);
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json(['status'=>false,'msg'=>$validator->errors()->first()],400);
        }

        event(new Registered($user = $this->create($request->all())));
        $msg = 'Congratulations! You have registered your account successfully, shortly you will receive an email to activate your account.';
        return response()->json(['status'=>true,'msg'=>$msg,'data'=>[]],200);
    }

    public function createPartner(Request $request){
        $request->request->add(['user_type_id' => 3]);
        $validator = $this->partnerValidator($request->all());

        if ($validator->fails()) {
            return response()->json(['status'=>false,'msg'=>$validator->errors()->first()],400);
        }

        $full_name = explode(" ", $request->company_name);
        $first_name =  $full_name[0];
        array_shift($full_name);
        $user = new User;
        $user->first_name = ucfirst($first_name);
        $user->last_name = ucfirst(join(" ",$full_name));
        $user->name = ucwords($request->company_name);
        $user->email = strtolower($request->email);
        $user->phone = $request->phone;
        $user->token = str_random(40) . time();
        $user->status = User::INACTIVE;
        $user->is_assigned = 'Y';
        $user->contact_person_name = $request->contact_person_name;
        $user->password = bcrypt($request->password);
        $user->user_type_id = $request->user_type_id;

        if($user->save()){
            $get_view_data['subject']    =   'Activate Account!';
            $get_view_data['view']       =   'mails.main';
            $get_view_data['user']       =   $user;

            Mail::to($user->email)->send(new MainTemplate( $get_view_data ));
            $msg = 'Congratulations! You have registered your account successfully, shortly you will receive an email to activate your account.';
            return response()->json(['status'=>true,'msg'=>$msg,'data'=>[]],200);
        }
    }

    public function forgetPassword(Request $request){
        $validator = Validator::make($request->all(), ['email' => 'required|email']);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'msg'=>$validator->errors()->first()],400);
        }

        $user = User::where('email', $request->only('email'))->first();
        if (!$user) {
            return response()->json(['status'=>false,'msg'=> 'You are not registered with us'],400);
        }

        $token = Password::broker()->createToken($user); //create reset password token
        $send = Mail::to($user->email)->send(new MailResetPasswordNotification($user, $token));

        return response()->json(['status'=>true,'msg'=> 'We have sent an activation link to your registered email Id.'],200);
    }
}
