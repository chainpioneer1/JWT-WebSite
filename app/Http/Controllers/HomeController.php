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
        /////////////// curl test //////////////
        $user = Auth::user();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('URL_SLICE')."/api/v1/jb/api-token-auth/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n".env('ADMIN_EMAIL')."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"password\"\r\n\r\n".env('ADMIN_PWD')."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
//                "Postman-Token: 6ffbfa29-0b9d-464e-b845-424d0b3f1082",
                "cache-control: no-cache",
                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {

        }

        $resp = \GuzzleHttp\json_decode($response);
//        echo "first call result";
//        echo $resp->token;
//
//        // ================================ get user token ==============================;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://preverity-staging.juiceboxdata.com/api/v1/jb/users/".$user->email."/token/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "Authorization: JWT ".$resp->token,
//                "Postman-Token: 3787e8fd-d416-4c93-a99f-ba72144150f1",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
//            echo "second call result";
//            echo $response;
        }
        $resp_2 = \GuzzleHttp\json_decode($response);
//        //====================================== get embedded url========================;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://preverity-staging.juiceboxdata.com/api/v1/jb/apps/cbebbd0e/embed/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "{\r\n\"token\":\"".$resp_2->token."\"\r\n}\r\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: JWT ".$resp->token,
                "Content-Type: application/json",
//                "Postman-Token: da22c9dc-0634-467f-8561-dc37a32b6b15",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
//            echo "third call result";
//            echo $response;
        }
        $embedded_url = \GuzzleHttp\json_decode($response)->url;
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
