<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use JWT;
use Auth;
use Hash;
use App\User;
use App\Http\Controllers\UserFcmTokenController as FcmToken;
use DateTimeImmutable;

class ApiLoginController extends FcmToken
{
	protected $fcmToken;

	public function __construct(FcmToken $FcmToken){
        $this->fcmToken = $FcmToken;
    }

    /**
     * Login Here
     */
	public function postSignin(Request $request){
		$validate = Validator::make($request->all(),[
			'email'=>'required',
			'password'=>'required',
			//'token'=>'required',
			'device_type' => 'sometimes|required|in:ios'
		]);

		if($validate->fails()){
			return response()->json(['message'=>$validate->errors()->first()],422);
		}

		$credential = $request->only('email','password');
		$credential['status'] = \App\User::ACTIVE;
		$credential['user_type_id'] = '7';

		$user = User::where(['email' => $request->only('email'), 'status' => \App\User::ACTIVE, 'user_type_id' => '7'])->first();
		if (empty($user)) {
			return response()->json(['message'=>'Invalid User','status'=>false],400);
		}

		if (!$user || !Hash::check($credential['password'], $user->password)) {
		    return response()->json(['message'=>'Authentication error: Please check your credentials','status'=>false],400);
		}

		// dd($user);
		try {
			if(!$token = JWTAuth::attempt($credential,['exp' => \Carbon\Carbon::now()->addDays(7)->timestamp])){
				return response()->json(['message'=>'Unauthorized','status'=>false],400);
			}
		} catch (JWTException $e) {
			return response()->json(['message'=>$e->getMessage(),'status'=>false],400);
		}

		$user->user_id = (string)$user->id;
		$user->user_type_id = (string)$user->user_type_id;

		if($user->hasMeta('_client_logo')){
			$user->client_logo = url(asset($user->getMeta('_client_logo')));
		}


		# store fcm token to push notification
		//$user->token_response = $this->fcmToken->store($request->all()); 

		return response()->json(['token'=>$token,'status'=>true,'message'=>"You have successfully logged in",'data'=>$user],200);
	}

	public function changePassword(Request $request){
		$validate = Validator::make($request->all(),[
			'password'=>'required',
			'confirm_password'=>'required|same:password',
		]);
		if($validate->fails()){
			return response()->json(['message'=>$validate->errors()->first()],422);
		}

		$user = \App\User::find(Auth::id());
		$user->password = bcrypt($request->password);
		$user->save();
		return response()->json(['status'=>true,'message'=>"Your password has been changed successfully"],200);
	}
}
