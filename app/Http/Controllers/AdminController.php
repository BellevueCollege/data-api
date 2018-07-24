<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Adldap\AdldapInterface;
use Adldap;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * @var \Adldap\AdldapInterface
     */
    protected $ldap;

    public function __construct(AdldapInterface $adldap)
    {
        $this->ldap = $adldap;
    }

    public function index()
    {
        return view('admin.index');
    }

    public function loginShow()
    {
        return view('admin.login');
    }

    public function loginPost(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required',
            'password' => 'required',
        ]);

        $req_creds = $request->only('email', 'password');
        if (Adldap::getProvider('default')->auth()->attempt($req_creds['email'], $req_creds['password'])) {
            // Passed!
            //$user = Adldap::user();

            return redirect()->to('admin');
        }
        
        /*if (Auth::attempt($request->only(['email', 'password']))) {
        
            // Returns \App\User model configured in `config/auth.php`.
            $user = Auth::user();
            
            
        }*/
        
        $request->session()->put('key', 'a value');
        //dd($request->session());
        //redirect()->setSession($request->session());
        //redirect()->setSession($request->session());
        return redirect()->to('admin/login')->withErrors('Hmm... Your username or password is incorrect');//->setSession($request->session());//->withErrors('Hmm... Your username or password is incorrect');

        //return view('admin.index'); //response()->json(compact('token'));
    }
}