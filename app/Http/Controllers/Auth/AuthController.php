<?php

namespace App\Http\Controllers\Auth;

use App\Guru;
use Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    
    /**
     * Tampilkan form login.
     * 
     * @return View
     */
    public function form(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('panel_utama');
        }

        return view('login');
    }

    /**
     * Melakukan proses login.
     * 
     * @param Request $request
     */
    public function login(Request $request)
    {
        if (Auth::attempt([
            'username' => $request->input('username'),
            'password' => $request->input('password')
        ], (bool) $request->input('remember_me'))) {
            return redirect()->intended('/');
        } else {
            return back()->with('message', 'Login gagal; cek kembali username dan password.');
        }
    }

    /**
     * Logout dari akun.
     */
    public function logout()
    {
        Auth::logout();

        return redirect()->route('halamanLogin');
    }
    
    public function restrict()
    {
        Auth::logout();
        
        return redirect()->route('halamanLogin')->with('message', 'Akun Anda bukan termasuk akun staf.');
    }
}
