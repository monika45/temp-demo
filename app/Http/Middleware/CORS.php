<?php

namespace App\Http\Middleware;

use Closure;

class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        header('Access-Control-Allow-Origin: *');
//        $headers = [
//            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
//            'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
//        ];
//        if($request->getMethod() == "OPTIONS") {
//            return Response::make('OK', 200, $headers);
//        }
//
//        $response = $next($request);
//        foreach($headers as $key => $value)
//            $response->header($key, $value);
//        return $response;


//        return $next($request);

        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
        $response->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        $response->header('Access-Control-Allow-Credentials', 'true');
        return $response;


    }
}
