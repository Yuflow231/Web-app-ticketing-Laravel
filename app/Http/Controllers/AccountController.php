<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function login()
    {
        return view('login');
    }
    public function create()
    {
        return view('create-account');
    }
    public function password()
    {
        return view('reset-password');
    }
    public function profile()
    {
        return view('profile');
    }

}
