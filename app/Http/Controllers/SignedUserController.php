<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class SignedUserController extends Controller
{
    public function signed(User $user) 
    {
        auth()->login($user);
        session()->regenerate();
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
