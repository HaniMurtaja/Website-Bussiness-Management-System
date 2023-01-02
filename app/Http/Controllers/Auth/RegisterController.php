<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function redirectTo(){
        return route('user.home');
    }


    public function index()
    {

    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:191'],
            'captcha_token' => ['required'],
            'username' => ['required', 'string', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ],[
            'captcha_token.required' => __('google captcha is required'),
            'name.required' => __('name is required'),
            'name.max' => __('name is must be between 191 character'),
            'username.required' => __('username is required'),
            'username.max' => __('username is must be between 191 character'),
            'username.unique' => __('username is already taken'),
            'email.unique' => __('email is already taken'),
            'email.required' => __('email is required'),
            'password.required' => __('password is required'),
            'password.confirmed' => __('both password does not matched'),
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
         return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'country' => $data['country'],
            'city' => $data['city'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
