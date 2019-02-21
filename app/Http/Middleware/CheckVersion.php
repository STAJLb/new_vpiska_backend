<?php

namespace App\Http\Middleware;

use Closure;

class CheckVersion
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
        if(!is_null($request->header('Version'))){
            $version = $request->header('Version');
            if(CheckVersion::checkVersion($version)){
                $response['error'] = true;
                $response['exp_access_token'] = false;
                $response['error_msg'] = 'Текущая версия приложения больше не поддерживается, пожалуйста, скачайте актуальную версию с Play Market.';

                return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
            }
        }else if(!is_null($request->version)){
            $version = $request->version;

            if(CheckVersion::checkVersion($version)){
                $response['error'] = true;
                $response['exp_access_token'] = false;
                $response['error_msg'] = 'Текущая версия приложения больше не поддерживается, пожалуйста, скачайте актуальную версию с Play Market.';

                return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
            }
        }else{
            $response['error'] = true;
            $response['exp_access_token'] = false;
            $response['error_msg'] = 'Текущая версия приложения больше не поддерживается, пожалуйста, скачайте актуальную версию с Play Market.';

            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }


        return $next($request);
    }

    public function checkVersion($version){
        if($version >= env("MINIMAL_VERSION_CODE", "")){
                return false; //Проверка  пройдена
            }
            return true;

    }
}
