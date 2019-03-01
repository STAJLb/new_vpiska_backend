<?php

namespace App\Http\Controllers\Api\v2;

use App\AppUser;
use App\Guest;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class GuestController extends Controller
{
    public function loginGuest(Request $request){

        $validator = Validator::make($request->all(), [

            'imei' =>'bail|required',

        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $response['error'] = true;
            $response['error_msg'] = $errors;
            return json_encode($response);
        }

        $imei = $request->imei;
//        $reason = Guest::checkImeiBanForRegister($imei);
//        if($reason != null){
//            $response = array(
//                'error' => true,
//                'error_msg' => "Ваш акканут заблокирован. По причине: ".$reason
//            );
//            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
//        }

        if(Guest::checkExitUser($imei)){
            $user = Guest::select('id')->where('imei',$imei)->first();

            $userId = $user->id;

            $response["error"] = false;
            $response["data_tokens"] = AppUser::createDataTokens($userId);
        }else{
            Guest::addUser($imei);
            $user = Guest::select('id')->where('imei',$imei)->first();

            $userId = $user->id;

            $response["error"] = false;
            $response["data_tokens"] = AppUser::createDataTokens($userId);
        }
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);

    }
}
