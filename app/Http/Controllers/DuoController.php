<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class DuoController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function duoLogin(Request $request)
    {
        $this->validate($request, [
            'email'=>'required|email|exists:users',
            'password'=>'required|required'
        ]);


        $user = array(
            'email'=>$request->get('email'),
            'password'=>$request->get('password')
        );
        $aKey = env('DUO_AKEY');
        $iKey = env('DUO_IKEY');
        $sKey = env('DUO_SKEY');
        if(Auth::validate($user))
        {
            $u = $request->get('email');
            $duoinfo = array(
                'HOST'=>env('DUO_API_HOST'),
                'POST'=>url('postLogin'),
                'USER'=>$u,
                'SIG'=>\Duo\Web::signRequest($iKey, $sKey, $aKey, $u)
            );

            return view('auth.duologin', compact('duoinfo'));
        }
        else
        {
            return redirect()->back()->with('message', 'Your username and/or password was incorrect')->withInput();
        }
    }

    public function duoregister(Request $request)
    {
        $this->validate($request, [
            "email"=>"required|string|email|unique:users",
            "password"=>"required|string|min:6|confirmed"
        ]);
        User::create([
//            'name' => $data['name'],
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'IP'=>$request->get('ip')
        ]);
        return redirect('login')->with('status', 'Registered successfully');
    }

    public function postLogin(Request $request)
    {
        $aKey = env('DUO_AKEY');
        $iKey = env('DUO_IKEY');
        $sKey = env('DUO_SKEY');

        $response = $request->get('sig_response');
        $u = \Duo\Web::verifyResponse($iKey, $sKey, $aKey, $response);
        /**
         * Get the id of the authenticated user from their email address
         */
        if($u){
            $user = User::where('email', $u)->first();
            $userid = $user->id;

            /**
             * Log the user in by their ID
             */

            Auth::loginUsingId($userid);

            /**
             * Check Auth worked, redirect to homepage if so
             */
            if(Auth::check()){
                return redirect('home')->with('message', 'You have successfully logged id');
            }
        }

        /**
         * Otherwise, Auth failed, redirect to front page with message
         */
        return redirect('/')->with('message', 'Unable to authenticate you.');
    }
}
