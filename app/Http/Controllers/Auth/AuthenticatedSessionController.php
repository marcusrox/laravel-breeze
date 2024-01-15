<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use App\Models\User;
use App\Notifications\UserUrlSignedNotification;
use Illuminate\Support\Facades\URL;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::whereEmail($request->email)->first();
        if (!$user) 
        {
            return back()->withErrors(['email' => 'Conta inválida']);
        }

        // Gerar URL assinada
        $urlSigned = URL::temporarySignedRoute('url.signed', now()->addHour(1), ['user' => $user->code]);
        //dd($urlSigned);

        // Mandar email pra o usuário
        $user->notify(new UserUrlSignedNotification($urlSigned));

        //return redirect()->intended(RouteServiceProvider::HOME);
        return redirect('login')->with('status', 'Enviamos os dados de acesso para o seu e-mail');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
