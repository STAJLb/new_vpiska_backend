<?php
/**
 * Created by PhpStorm.
 * User: Кирилл
 * Date: 06.02.2019
 * Time: 23:01
 */

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AppUser;
use App\Feedback;
use Validator;
use Log;


class FeedbackController  extends Controller
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
        $validator = Validator::make($request->all(), [
            'feedback' => 'bail|required|max:600'

        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $response['exp_access_token'] = false;
            $response['error'] = true;
            $response['error_msg'] = $errors;
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        $response['exp_access_token'] = false;

        $token = $request->access_token;
        $uid = AppUser::getUserId($token);
        $textFeedback = $request->feedback;


       $feedback = new Feedback();
       $feedback->uid = $uid;
       $feedback->feedback = $textFeedback;
       $feedback->save();

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