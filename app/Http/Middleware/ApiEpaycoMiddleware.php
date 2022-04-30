<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use Illuminate\Http\Request;

class ApiEpaycoMiddleware
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
        if(!isset($_SERVER['HTTP_X_HARDIK'])){
            return Response::json(array('error'=>'Acceso no permitido'));
        }

        if($_SERVER['HTTP_X_HARDIK'] != 'api-key-epayco'){
            return Response::json(array('error'=>'Encabezado personalizado incorrecto'));
        }

        return $next($request);
    }
}
