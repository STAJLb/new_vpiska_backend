<?php

namespace App\Http\Controllers\Api\v2;

use App\AppUser;
use App\Purchase;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;

class PurchaseController extends Controller
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

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('Данные пришли'.$request);
       if(Purchase::createPurchase($request)){
           $response["exp_access_token"] = false;
           $response["error"] = false;
           $response["message"] = "Ваша покупка зарегистрирована. Спасибо за поддержку проекта!";

           return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
       }else{
           $response["exp_access_token"] = false;
           $response["error"] = false;
           $response["message"] = 'Ошибка на сервере, пожалуйста, обратитесь к администратору.';

           return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
       }
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
