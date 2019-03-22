<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class DuoController extends Controller
{
    public function __construct()
    {
    }

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
                'POST'=>URL::to('/').'/login',
                'USER'=>$u,
                'SIG'=>\Duo\Web::signRequest($iKey, $sKey, $aKey, $u)
            );

            return view('auth.duologin', compact('duoinfo'));
        }


    }
}
