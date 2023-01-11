<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $api_key = $request->input('token');
        
        if (!empty($api_key)) {
            $check_api_key = User::where('api_key', '=', $api_key)->get();

            if (count($check_api_key) == 1) {
                $request->attributes->add(['auth' => $check_api_key]);
                return $next($request);
            }
            abort(403);
        } else {
            abort(403);
        }
    }
}
