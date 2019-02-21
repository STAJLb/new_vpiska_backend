<?php

namespace App\Http\Controllers\Api\v2;

use App\Party;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AppUser;
use Log;
use App\Member;
use \Carbon\Carbon;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $response['exp_access_token'] = false;
        $response["error"] = false;
        $response["rating"] = AppUser::select('first_name','nik_name','rating','id')->orderBy('rating','desc')->limit(20)->get();

        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
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
    public function update(Request $request,$id)
    {
        $response['exp_access_token'] = false;
        Log::info('Данные на обновления рейтинга'.$request);
        $rating = $request->rating;
        $partyId = $request->party_id;
        $memberId = $id;

        if($rating > 10 || $rating < - 10){
            $response["error"] = true;
            $response["error_msg"] = "Неверное значение ретйинга.";
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        $member = Member::where(['id_party'=>$partyId,'id_user'=>$memberId])->first();

        $party = Party::find($partyId);


        if($rating == 0){
            $member->status = 2; //Если не захотел ставить оценку
        }else{
            $member->status = 1; //Если поставил оценку
        }
        $member->save();


        $user = AppUser::find($party->created_id);
        $user->rating = $user->rating + $rating;
        $user->save();

        $response["error"] = false;
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

    public function checkUpdateRating(Request $request){
        Log::info('Данные обновления рейтинга'.$request);
        $token = $request->header('Access-Token');
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
