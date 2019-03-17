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
        return view('home');
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
