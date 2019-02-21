<?php

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Review;
use App\AppUser;
use Validator;
use Log;

class ReviewController extends Controller
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
        Log::debug('Метод: '.$_SERVER['REQUEST_METHOD'].'Данные:'.$request);
        $response['exp_access_token'] = false;
        $message = $request->message;
        $idParty = $request->id_party;
        $access_token = $request->access_token;
        $idUser = AppUser::getUserId($access_token);;

        $validator = Validator::make($request->all(), [
            'message' => 'bail|required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $response['error'] = true;
            $response['error_msg'] = $errors;
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        $user = AppUser::find($idUser);


        $review = new  Review();
        $review->id_party = $idParty;
        $review->id_user = $idUser;
        $review->message = $message;
        $review->name_user = $user->first_name." (".$user->nik_name.")";
        $review->save();

        $response["error"] = false;
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response['exp_access_token'] = false;
        $response["error"] = false;
        $response["reviews"] = Review::where("id_party",$id)->get();
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
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
    public function update(Request $request, $id)
    {
        //
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
