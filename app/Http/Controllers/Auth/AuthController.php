<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Hash;
use Auth;

class AuthController extends Controller
{
    public function register()
    {
		return view('auth.register');
    }

    public function storeUser(Request $request)
    {

    	$request->validate([
    		'name'  => 'required|string|max:255',
    		'email'  => 'required|string|email|max:255|unique:users',
    		'password'  => 'required|string|min:8|confirmed',
    		'password_confirmation'  => 'required',
    	]);

    	User::create([
    		'name' => $request->name,
    		'email' => $request->email,
    		'password' => Hash::make($request->password),
    		'status' => 1,
    	]);

    	return redirect('/')->with("success","registered successfully");
    }

     public function login()
    {

      return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('post');
        }

        return redirect('login')->with('error', 'Oppes! You have entered invalid credentials');
    }

    public function logout() 
    {
      Auth::logout();
      return redirect('login');
    }

    public function home()
    {
      return redirect('post');
    }
}
