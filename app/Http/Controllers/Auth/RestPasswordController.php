<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class RestPasswordController extends Controller
{
    
    use RestsPasswords;

    protected $redirectTo = RouteServiceProvider::HOME;
}
