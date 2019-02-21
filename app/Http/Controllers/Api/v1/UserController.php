<?php

namespace App\Http\Controllers\Api\v1;

use App\AppUser;
use App\Note;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator;
use App\member;
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

    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'bail|required|min:2',
            'nik_name' => 'bail|required|min:2',
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
        $imei = $request->imei;
        $age = $request->age;
        $image = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';

        $error = AppUser::checkExitUser($nikName);
        if ($error == true) {
            $response = array(
                'error' => true,
                'error_msg' => "Никнейм уже занят."
            );
            return json_encode($response);
        }

        $reason = AppUser::checkImeiBanForRegister($imei);
        if($reason != null){
            $response = array(
                'error' => true,
                'error_msg' => "Ваш акканут заблокирован. По причине: ".$reason
            );
            return json_encode($response);
        }

        if (AppUser::addUser($first_name, $nikName, $password, $sex,$age ,$imei,$image) == true) {
            $response = array(
                'error' => true,
                'error_msg' => "Ошибка в процессе регистрации."
            );
            return json_encode($response);
        }



        $user = AppUser::getInfoAboutUser($nikName);
        $response["error"] = false;
//        $response["uid"] = $user->id;
//        $response["user"]["first_name"] = $user->first_name;
//        $response["user"]["nik_name"] = $nikName;
//        $response["user"]["sex"] = $sex;
//        $response["user"]["age"] = $age;
//        $response["user"]["created_at"] = $user->created_at->toDateString();
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);


        //Метод для создания пользователя
        //Путь /users/register
    }

    public function loginUser(Request $request){
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

    public function updateDataTokens(Request $request){
        $refresh_token = $request->refresh_token;

        $response["error"] = false;
        $response["data_tokens"] = AppUser::updateDataTokens($refresh_token);

        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    public function updateUser(Request $request){
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
        $user->first_name = $request->first_name;
        $user->nik_name = $request->nik_name;
        $user->sex = $request->sex;
        $user->age = $request->age;
        $user->save();


        $response["error"] = false;
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    public function getAge($y, $m, $d) {
        if($m > date('m') || $m == date('m') && $d > date('d'))
            return (date('Y') - $y - 1);
        else
            return (date('Y') - $y);
    }

    public function updateAvatar(Request $request){
        $response['exp_access_token'] = false;

        if ($request->hasFile('file')) {
            $time = str_replace('"','',$request->time);
            $ext = str_replace('"','',$request->ext);
            $fileName = $request->id.$time.$ext;

            $token = $request->access_token;

            if($token{0} == '"'){
                $token = substr($token, 1, -1);
            }
            $userId = AppUser::getUserId($token);

            $user = AppUser::find($userId);
            $user->image = 'https://clickcoffee.ru/avatars/'.$fileName;
            $user->save();

            if($request->file->move('avatars/', $fileName)){
                $success = true;
                $message = "Успешно загружено.";
            }else{
                $success = false;
                $message = "Ошибка загрузки изображения.";
            }
        }else{
            $success = false;
            $message = "Ошибка загрузки фото. Обратитесь к администратору.";
        }

        $response = array();



        $response["success"] = $success;
        $response["message"] = $message;
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);

    }

    public function getRatingTable()
    {
        $response['exp_access_token'] = false;
        $response["error"] = false;
        $response["rating"] = AppUser::select('first_name','nik_name','rating','id')->orderBy('rating','desc')->limit(20)->get();

        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    public function updateRatingUser(Request $request){
        $response['exp_access_token'] = false;

        $rating = $request->rating;
        $partyId = $request->party_id;
        $memberId = $request->member_id;

        if($rating > 10 || $rating < - 10){
            $response["error"] = false;
            $response["error_msg"] = "Неверное значение ретйинга.";
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        $partyMember = member::where('id_party',$partyId)->where('id_user',$memberId)->first();

        if($rating == 0){
            $partyMember->status = 2; //Если не захотел ставить оценку
        }else{
            $partyMember->status = 1; //Если поставил оценку
        }
        $partyMember->save();


        $user = AppUser::find($memberId);
        $user->rating = $user->rating + $rating;
        $user->save();

        $response["error"] = false;
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);

    }

    public function getMyProfile(Request $request){
        $token = $request->access_token;
        $userId = AppUser::getUserId($token);
        $user =  AppUser::select('first_name','nik_name','sex','age','image')->where('id',$userId)->first();

        $response["error"] = false;
        $response["exp_access_token"] = false;
        $response["user"] = $user;
        $response["notes"] = Note::where('uid',$userId)->where('read_status',0)->get();
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    public function updateStatusNote(Request $request){
        $note = Note::find($request->id_note);
        $note->read_status = 1;
        $note->save();

        $response['exp_access_token'] = false;
        $response["error"] = false;
        $response["message"] = "Ваш ответ зафиксирован.";
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);

    }

    public function checkUpdateRating(Request $request){
        $token = $request->access_token;
                 Log::info('Token midl'.$token);
        $userId = AppUser::getUserId($token);
        $user =  AppUser::where('id',$userId)->first();

        if(is_null($user->date_update_rating)){
            $rating =  $user->rating + 10 ;
            $user->rating =  $rating;
            $user->date_update_rating = Carbon::now();
            $user->save();

            $response['exp_access_token'] = false;
            $response["error"] = true;
            $response["update_rating"] = true;
            $response["message"] = "Ваш рейтинг обновлен. Добавлено 10 единиц.";

            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        if($user->date_update_rating < (Carbon::now()->subDay(1))){
            $rating =  $user->rating + 10 ;
            $user->rating =  $rating;
            $user->date_update_rating = Carbon::now();
            $user->save();
            $response["update_rating"] = true;
            $response["message"] = "Ваш рейтинг обновлен. Добавлено 10 единиц .";
        }else{
            $response["update_rating"] = false;
        }

        $response['exp_access_token'] = false;
        $response["error"] = false;

        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }
}
