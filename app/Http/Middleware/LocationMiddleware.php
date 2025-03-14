<?php

namespace App\Http\Middleware;

use App\Helper\Dropshipzone;
use App\Models\DropshipzoneToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LocationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && loginUser()->role == User::ROLE_LOCATION) {
            return $next($request);
        } else {
            return redirect()->route("login");
        }
    }
}
