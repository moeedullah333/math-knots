<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Session;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return Response()->json(["status"=>false,'msg'=>'Token is Wrong OR Did not Exist!']);
        // return view('auth.login');
    }
    // public function create(): View
    // {
        
    //     return view('auth.login');
    // }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if(auth()->user()->isAdmin() == true){
           
            return redirect()->intended(RouteServiceProvider::HOME);
        }else{
           
            Session::flash('message', 'You have logged In  Successfully'); 
            Session::flash('alert-class', 'alert-success');
            return redirect('/');
        }

        
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
