<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    use AuthenticatesUsers;

  
    protected $redirectTo = '/';

    public function redirectTo(){
        return route('user.home');
    }


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
    }
  


    public function username() {
        return 'username';
    }



    public function showAdminLoginForm(){
        return view('auth.admin.login');
    }


    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'username'   => 'required|string',
            'password' => 'required|min:6'
        ],[
            'username.required'   => __('username required'),
            'password.required' => __('password required')
        ]);

        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password], $request->get('remember'))) {

            return response()->json([
               'msg' => __('Login Success Redirecting'),
               'type' => 'success',
               'status' => 'ok'
            ]);
        }
        return response()->json([
            'msg' => __('Your Username or Password Is Wrong !!'),
            'type' => 'danger',
            'status' => 'not_ok'
        ]);
    }
    

    public function showLoginForm()
    {
        return view('frontend.user.login');
    }

    
}
