<?php

namespace App\Http\Controllers\Api;

use App\AppUser;
use App\Note;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator;
use App\member;
use App\Http\Controllers\Api\TokenController;


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
                'error_msg' => "Имя пользователя уже занято."
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
        $response["uid"] = $user->id;
        $response["user"]["first_name"] = $user->first_name;
        $response["user"]["nik_name"] = $nikName;
        $response["user"]["sex"] = $sex;
        $response["user"]["age"] = $age;
        $response["user"]["created_at"] = $user->created_at->toDateString();
        return json_encode($response);


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

        $reason = AppUser::checkImeiBan($nikName);
        if($reason != null){
            $response = array(
                'error' => true,
                'error_msg' => "Ваш акканут заблокирован. По причине: ".$reason
            );
            return json_encode($response);
        }

        $reason = AppUser::checkStatus($nikName);
        if($reason != null){
            $response = array(
                'error' => true,
                'error_msg' => "Ваш акканут заблокирован. По причине: ".$reason
            );
            return json_encode($response);
        }

            $user = AppUser::getInfoAboutUser($nikName);

        $response["error"] = false;
        $response["uid"] = $user->id;
        $response["user"]["first_name"] = $user->first_name;
        $response["user"]["nik_name"] = $user->nik_name;
        $response["user"]["image"] = $user->image;
        $response["user"]["sex"] = $user->sex;
        $response["user"]["age"] = $user->age;
        $response["user"]["created_at"] = $user->created_at->toDateString();

        return json_encode($response);

    }

    public function updateUser(Request $request){

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
            return json_encode($response);
        }

        $user = AppUser::find($request->uid);
        $arr = explode("-",$request->age);
        $user->first_name = $request->first_name;
        $user->nik_name = $request->nik_name;
        $user->sex = $request->sex;
       // $user->age = $this->getAge($arr[2],$arr[1],$arr[0]);
        $user->age = $request->age;
        $user->save();


        $response["error"] = false;
        $response["uid"] = $user->id;
        $response["user"]["first_name"] = $user->first_name;
        $response["user"]["nik_name"] = $user->nik_name;
        $response["user"]["image"] = $user->image;
        $response["user"]["sex"] = $user->sex;
        $response["user"]["age"] = $user->age;
        $response["user"]["created_at"] = $user->created_at->toDateString();

        return json_encode($response);
    }

    public function getAge($y, $m, $d) {
        if($m > date('m') || $m == date('m') && $d > date('d'))
            return (date('Y') - $y - 1);
        else
            return (date('Y') - $y);
    }

    public function updateAvatar(Request $request){

        if ($request->hasFile('file')) {
            $time = str_replace('"','',$request->time);
            $ext = str_replace('"','',$request->ext);
            $fileName = $request->id.$time.$ext;

            $user = AppUser::find($request->id);
            $user->image = 'http://clickcoffee.ru/avatars/'.$fileName;
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
        return json_encode($response);

    }

    public function getRatingTable()
    {
        $response["error"] = false;
        $response["rating"] = AppUser::select('first_name','nik_name','rating')->orderBy('rating','desc')->get();

        return json_encode($response);
    }

    public function updateRatingUser(Request $request){

        $rating = $request->rating;
        $partyId = $request->party_id;
        $memberId = $request->member_id;

        if($rating > 10 || $rating < - 10){
            $response["error"] = false;
            $response["error_msg"] = "Неверное значение ретйинга.";
            return json_encode($response);
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
        return json_encode($response);

    }

    public function getMyProfile($id){
        $user =  AppUser::select('first_name','nik_name','sex','age','image')->where('id',$id)->first();

        $response["error"] = false;
        $response["user"] = $user;
        $response["notes"] = Note::where('uid',$id)->where('read_status',0)->first();
        return json_encode($response);
    }

    public function updateStatusNote(Request $request){
        $note = Note::find($request->id_note);
        $note->read_status = 1;
        $note->save();

        $response["error"] = false;
        $response["message"] = "Ваш ответ зафиксирован.";
        return json_encode($response);

    }
}
