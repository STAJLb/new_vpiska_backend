<?php

namespace App\Http\Controllers\Api\v2;

use App\AppUser;
use App\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Log;

use \Carbon\Carbon;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAgreement(){
        return view('agreement');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request)
    {
        Log::info('Данные регистрации'.$request);

        $imei = $request->imei;
        if($request->type == 'auth'){
            $validator = Validator::make($request->all(), [
                'first_name' => 'bail|required|min:2|alpha_num',
                'nik_name' => 'bail|required|min:2|alpha_num',
                'password' => 'bail|required|min:6',
                'sex' =>'bail|required',
                'imei' =>'bail|required',
                'age' =>'bail|required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response['error'] = true;
                $response['error_msg'] = $errors;
                return json_encode($response);
            }

            $first_name = $request->first_name;
            $nikName = $request->nik_name;
            $password = $request->password;
            $sex = $request->sex;

            $age = $request->age;
            $image = 'http://lumpics.ru/wp-content/uploads/2017/11/Programmyi-dlya-sozdaniya-avatarok.png';
        }else{

            $reason = AppUser::checkImeiBanForRegister($imei);
            if($reason != null){
                $response = array(
                    'error' => true,
                    'error_msg' => "Ваш акканут заблокирован. По причине: ".$reason
                );
                return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
            }
            if(AppUser::select('id')->where('imei',$imei)->first() == null) {
                if (AppUser::addGuest($imei) == false) {
                    $response = array(
                        'error' => true,
                        'error_msg' => "Ошибка в процессе регистрации."
                    );
                    return response()->json($response, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
                }
            }

            $user = AppUser::select('id')->where('imei',$imei)->first();

            $userId = $user->id;

            $response["error"] = false;
            $response["data_tokens"] = AppUser::createDataTokens($userId);

            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);

        }



        $error = AppUser::checkExitUser($nikName);
        if ($error == true) {
            $response = array(
                'error' => true,
                'error_msg' => "Никнейм уже занят."
            );
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        $reason = AppUser::checkImeiBanForRegister($imei);
        if($reason != null){
            $response = array(
                'error' => true,
                'error_msg' => "Ваш акканут заблокирован. По причине: ".$reason
            );
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        if (AppUser::addUser($first_name, $nikName, $password, $sex,$age ,$imei,$image) == false) {
            $response = array(
                'error' => true,
                'error_msg' => "Ошибка в процессе регистрации."
            );
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }



        $response["error"] = false;

        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);


        //Метод для создания пользователя
        //Путь /users/register
    }

    public function loginUser(Request $request){
        Log::info($request);
        $validator = Validator::make($request->all(), [
            'nik_name' => 'bail|required',
            'password' => 'bail|required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $response['error'] = true;
            $response['error_msg'] = $errors;
            return json_encode($response);
        }

        $nikName = $request->nik_name;
        $password = $request->password;

        if(AppUser::getUserLogin($nikName,$password) == null){
            $response = array(
                'error' => true,
                'error_msg' => "Данные введены неверно."
            );
            return json_encode($response);
        }

        $user = AppUser::select('id')->where('nik_name',$nikName)->first();

        $userId = $user->id;

        $response["error"] = false;
        $response["data_tokens"] = AppUser::createDataTokens($userId);


        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);

    }

    public function update(Request $request){
        $response['exp_access_token'] = false;

        $validator = Validator::make($request->all(), [
            'first_name' => 'bail|required|min:2',
            'nik_name' => 'bail|required|min:2',
            'sex' =>'bail|required',
            'age' => 'bail|required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();

            $response['error'] = true;
            $response['error_msg'] = $errors;

            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }
        $access_token = $request->access_token;
        $userId = AppUser::getUserId($access_token);

        $user = AppUser::find($userId);


        $nUser = (AppUser::where('nik_name',$request->nik_name)->first());

        if(is_null($nUser) || $userId == $nUser->id){
            $user->first_name = $request->first_name;
            $user->nik_name = $request->nik_name;
            $user->sex = $request->sex;
            $user->age = $request->age;

            $user->save();

            $response["error"] = false;
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }else{
            $response['error'] = true;
            $response['error_msg'] = 'Пожалуйста, выберите другой Никнейм.';

            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }




    }

    public function getAge($y, $m, $d) {
        if($m > date('m') || $m == date('m') && $d > date('d'))
            return (date('Y') - $y - 1);
        else
            return (date('Y') - $y);
    }


    public function show(Request $request){
        Log::info('Запрос на возврат данных профиля'.$request);
        $token = $request->header('Access-Token');
        $userId = AppUser::getUserId($token);

        if(!is_null($request->header('Uid'))){
            $userId = $request->header('Uid');
        }

        $user =  AppUser::select('first_name','nik_name','sex','age','image','rating','balance')->where('id',$userId)->first();

        $response["error"] = false;
        $response["exp_access_token"] = false;
        $response["user"] = $user;
        $response["notes"] = Note::where('uid',$userId)->where('read_status',0)->first();
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    public function updateBalance(Request $request){
        $token = $request->access_token;
        $addingMoney = $request->adding_money;
        $userId = AppUser::getUserId($token);
        $appUser = AppUser::find($userId);
        $appUser->balance =  $appUser->balance + $addingMoney;
        $appUser->save();

        $response["error"] = false;
        $response["exp_access_token"] = false;
        $response["message"] = 'Ваш баланс увеличен на '. $addingMoney.  ' монет. Спасибо за покупку!';
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    public function buyView(Request $request){
        $token = $request->access_token;

        $userId = AppUser::getUserId($token);
        $appUser = AppUser::find($userId);
        if ($appUser->balance  >= 100) {
            $appUser->balance = $appUser->balance - 100;
            $appUser->max_number_view = $appUser->max_number_view + 10;
            $appUser->save();
        }else{
            $response["error"] = true;
            $response["message"] = null;
            $response["exp_access_token"] = false;
            $response["error_msg"] = 'Пожалуйста,пополните баланс.';

            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        $response["error"] = false;
        $response["exp_access_token"] = false;
        $response["message"] = 'Добавлено 10 просмотров событий. Спасибо за покупку!';
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    public function buyAdding(Request $request){
        $token = $request->access_token;

        $userId = AppUser::getUserId($token);
        $appUser = AppUser::find($userId);
        if ($appUser->balance  >= 100){
            $appUser->balance =  $appUser->balance - 100;
            $appUser->max_number_adding_party = $appUser->max_number_adding_party + 1;
            $appUser->save();
        }else{
            $response["error"] = true;
            $response["exp_access_token"] = false;
            $response["error_msg"] = 'Пожалуйста,пополните баланс.';

            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }


        $response["error"] = false;
        $response["exp_access_token"] = false;
        $response["message"] = 'Куплено одно добавление события. Спасибо!';
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }





}
