<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null $guard
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $guard = 'admin'): Response
    {
        // Check if the user is authenticated using the given guard
        if (Auth::guard($guard)->guest()) {
            // If it's an AJAX or JSON request, return Unauthorized
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                // Redirect to the home page if not authorized
                return redirect(url('/'));
            }
        }

        // Proceed to the next middleware or controller
        $response = $next($request);

        // Set CORS headers for all responses from this middleware
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
        $response->headers->set('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache'); // HTTP 1.0
        $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT'); // Date in the past

        // Return the response
        return $response;
    }
}
