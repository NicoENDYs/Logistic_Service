<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class ThrottleTripLocations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $minutes = 15)
    {
        $tripid = $request->route('trip') ?? $request->input('trip_id');
        $userid= $request->user()->id ?? $request->ip();
        $key = "trip:{$tripid}:loc_last_by:{$userid}";

        if (Cache::has($key)){
            return back()->withErrors(['too many' => "Espere {$minutes} minutos"]);
        }

        //Permitir y bloquear por $minutos

        Cache::put($key, true, $minutes);

        return $next($request);
    }
}
