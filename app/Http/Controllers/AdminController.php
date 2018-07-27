<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin')->except('admin.logout');
        $this->middleware('auth:admin')->except('admin.login');
    }

    public function index()
    {
        return view('admin.index');
    }

    public function loginShow()
    {
        if ( Auth::check() ) {
            return redirect()->route('admin.index');
        }
        return view('admin.login');
    }

    public function loginPost(Request $request)
    {
        $this->validate($request, [
            'samaccountname'    => 'required',
            'password' => 'required',
        ]);

        $req_creds = $request->only('samaccountname', 'password');
        if ( Auth::guard('admin')->attempt($req_creds) ) {
            // Passed so send to admin dashboard!
            return redirect()->route('admin.index');
        }
        
        // Failed so send back to login
        return redirect()->route('admin.login')->withErrors('Your username or password is incorrect.');

    }
}