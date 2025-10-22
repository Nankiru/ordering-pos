<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeliveryAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('delivery_id')) {
            return redirect()->route('delivery.login.form');
        }
        return $next($request);
    }
}


