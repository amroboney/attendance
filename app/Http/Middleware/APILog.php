<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APILog
{

    public function handle(Request $request, Closure $next,  $guard = null)
    {
        $url = $request->url();
        $method = $request->method();
        \Log::channel('testApi')->info('Request Data');
        \Log::channel('testApi')->info($method .":" .$url);
        \Log::channel('testApi')->info($request->all());
       
        return $next($request);
    }

    public function terminate($request, $response)
    {    
        \Log::channel('testApi')->info('Response Data');
        \Log::channel('testApi')->info(json_encode($response));
    }
}
