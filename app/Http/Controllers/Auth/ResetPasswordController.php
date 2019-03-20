<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * Request email
     */
    public function request_email(Request $request)
    {
        $email = $request->get('email');
        $user = User::where('email', $email)->first();
        if(!$user){
            return redirect()->back()->with(['msg'=>'This email is not exist in our database.', 'status'=>false]);
        }
        $token = uniqid();
        $hashed_token = Hash::make($token);
        $user->token = $hashed_token;
        $user->save();
        $redirect_link = url('password/update').'?token='.$token.'&email='.$email;
        \Log::info("Please click following link to reset your password. \n ".$redirect_link);
        /** send mail */
        if(env('PRODUCTION') == 'yes'){
            Mail($user->email, 'Email validation for reset password', "Please click following link to reset your password. \n ".$redirect_link);
        }
        return redirect()->back()->with(['msg'=>'Please check your mail box', 'status'=>true, 'redirect_link'=>$redirect_link]);
    }

    /**
     * Update password
     */
    public function update(Request $request){
        $token = $request->get('token');
        $email = $request->get('email');
        return view('auth.passwords.reset', compact('email', 'token'));
    }

    public function reset_password(Request $request){
        \Log::info($request->all());
        $this->validate($request, [
            'password'=>'required|confirmed'
        ]);
        $token = $request->get('reset_token');
        $email = $request->get('email');
        $user = User::where('email', $email)->first();

        if(!$user){
            return redirect()->back()->with(["status"=>false, "msg"=>"This email is not exist in our database."]);
        }
        if(Hash::check($token, $user->token)){
            $user->password = Hash::make($request->get('password'));
            $user->save();
            return redirect('login');
        }else{
            return redirect()->back()->with(["status"=>false, "msg"=>"This email is not exist in our database."]);
        }
    }
}
