<?php
/**
 * Created by PhpStorm.
 * User: Кирилл
 * Date: 01.03.2018
 * Time: 19:18
 */

namespace App\Http\Middleware;
use Closure;
use Log;
use \Firebase\JWT\JWT;
use Request;

use App\AppUser;

class CheckToken
{
    public function handle($request, Closure $next)
    {
        $secretKey = ENV('APP_SECRET_KEY');

        if(!is_null($request->access_token)){
            $token = $request->access_token;
        } else {
            $token = $request->header('Access-Token');
        }
        Log::debug('Метод: '.$_SERVER['REQUEST_METHOD'].'Данные:'.$request);




        if(is_null($token)){
            $response['error'] = true;
            $response['exp_access_token'] = true;
            $response['error_msg'] = "У вашего приложения нет доступа к серверу. Скачайте официальную версию приложения или обновите текущую.";
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }



        JWT::$leeway = 60+20;
        try {
           JWT::decode($token, $secretKey, array('HS256'));
        } catch (\Exception $e) { // Also tried JwtException
            switch ($e->getMessage()) {
                case 'Expired token':
                    $response['error'] = true;
                    $response['exp_access_token'] = true;
                    $response['error_msg'] = "Время жизни токена истекло.";
                    return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
                    break;
                case 'Signature verification failed':
                    $response['error'] = true;
                    $response['error_msg'] = "Невеврный токен.";
                    return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
                    break;
                case 'Wrong number of segments':
                    $response['error'] = true;
                    $response['error_msg'] = "Неверный формат токена.";
                    return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
                    break;
            }
        }

        $dataUser =  JWT::decode($token, $secretKey, array('HS256'));
        $userId = $dataUser->data->userId;


        $reason = AppUser::checkBan($userId);
        if($reason != null){

            $response = array(
                'error' => true,
                'exp_access_token' => false,
                'error_msg' => "Ваш акканут заблокирован. По причине: ".$reason
            );
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

//        $reason = AppUser::checkStatus($userId);
//        if($reason != null){
//            $response = array(
//                'error' => true,
//                'exp_access_token' => false,
//                'error_msg' => "Ваш акканут заблокирован. По причине: ".$reason
//            );
//            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
//        }



        return $next($request);
    }
}