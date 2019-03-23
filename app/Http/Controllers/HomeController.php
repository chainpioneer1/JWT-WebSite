<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // ==================== Get Authentication token ================================
        $url_1 = 'https://healthyco.com/api/v1/jb/api-token-auth/';

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/josn',
                'content' => http_build_query(
                    array(
                        'email' => env('ADMIN_EMAIL'),
                        'password' => env('ADMIN_PWD')
                    )
                ),
                'timeout' => 60
            )
        ));

        $resp = file_get_contents($url_1, FALSE, $context);
        $resp = \GuzzleHttp\json_decode($resp);
        \Log::info('first call response..........'.var_export($resp, true));

        // ================================ get user token ==============================;
        $client_admin_token = $resp->token;
        $user = Auth::user();
        $user_email = $user->email;
        $resp_2 = file_get_contents("https://healthyco. com/api/v1/jb/users/".$user_email."/".$client_admin_token."/");
        $resp_2 = \GuzzleHttp\json_decode($resp_2);
        \Log::info('second call response................'.var_export($resp_2, true));
        $user_token = $resp_2->user_token;

        //====================================== get embedded url========================;
        $url_3 = 'https://healthyco.com/api/v1/jb/api-token-auth/';
        $context3 = stream_context_create(array(
            'http' => array(
                'method' => 'PUT',
                'header' => array('Content-type'=> 'application/josn', 'x-auth-token'=>$client_admin_token),
                'content' => http_build_query(
                    array(
                        'token' => $user_token
                    )
                ),
                'timeout' => 60
            )
        ));
        $resp3 = file_get_contents($url_3, FALSE, $context3);
        $resp3 = \GuzzleHttp\json_decode($resp3);
        \Log::info('third call response..........'.var_export($resp3, true));
        $embedded_url = $resp3->url;

        return view('home', compact('embedded_url'));
    }

    /**
     * Show the user's profile page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    /**
     * Save user's profile data into db
     *
     * @param Request
     */
    public function save_profile(Request $request)
    {
        $this->validate($request, [
            'Firstname' => ['string', 'max:255'],
            'Lastname' => ['string', 'max:255'],
            'IP' => ['string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $params = $request->all();
        $params['password'] = Hash::make($params['password']);
//        User::create($params);
        $userid = Auth::user()->id;
        $user = User::find($userid);
        $user->Firstname = $params['Firstname'];
        $user->Lastname = $params['Lastname'];
        $user->password = $params['password'];
        $user->IP = $params['IP'];
        $user->save();
        return redirect()->back()->with('success', 'Updated Successfully.');
    }


}
