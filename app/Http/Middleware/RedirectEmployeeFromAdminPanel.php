<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectEmployeeFromAdminPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check()
            && auth()->user()?->hasRole('employee')
            && $request->is('admin', 'admin/*')
        ) {
            return redirect('/employee');
        }

        return $next($request);
    }
}