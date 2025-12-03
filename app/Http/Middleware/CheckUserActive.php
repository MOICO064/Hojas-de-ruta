<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();


        if ($user && $user->estado !== 'ACTIVO') {
            Auth::logout(); 

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Se inhabilitó tu usuario. Contactate con administración.'
                ]);
        }

        return $next($request);
    }
}
