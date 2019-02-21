<?php

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AppUser;
use Illuminate\Support\Facades\Log;

class AvatarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        Log::debug($request);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
