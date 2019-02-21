<?php

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Party;
use App\Member;
use App\AppUser;
use Carbon\Carbon;

class MemberController extends Controller
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
        $idParty = $request->id_party;

        $token = $request->access_token;
        $idMember = AppUser::getUserId($token);

        $response['exp_access_token'] = false;


        if(Party::find($idParty) != null){
            $party = Party::find($idParty);

            if(($party->count_people) < ($party->max_count_people)){
                if(member::where('id_user',$idMember)->where('id_party',$idParty)->count() == 0){


                    if($idMember == $party->created_id){
                        $response["error"] = true;
                        $response["error_msg"] = "Вы не можете присоединиться к своей вписке.";
                        return json_encode($response);
                    }

                    if(Carbon::today() >= $party->date_time){
                        $response["error"] = true;
                        $response["error_msg"] = "Вы не можете изменять данные в день начала вписки.";
                        return json_encode($response);
                    }

                    $lastPartyMember = member::orderBy('id', 'desc')->first();
                    $member = AppUser::find($idMember);
                    $nameMember = $member->first_name."(".$member->nik_name.")";
                    $newId = 0;
                    $newPartyMember = new member();
                    if($lastPartyMember != null) {
                        $newId = $lastPartyMember->id;
                    }
                    $newPartyMember->id = $newId + 1;
                    $newPartyMember->id_party = $idParty;
                    $newPartyMember->id_user = $idMember;
                    $newPartyMember->name_member = $nameMember;
                    $newPartyMember->save();
                    $party->count_people = $party->count_people + 1;
                    $party->save();

                    $response["error"] = false;
                    $response["msg"] = "Вы добавлены.";

                    $response["party"] = Party::find($idParty);
                    return json_encode($response);

                }else{
                    $partyMember = member::where('id_user',$idMember)->where('id_party',$idParty)->first();
                    $partyMember->delete();
                    $party->count_people = $party->count_people - 1;
                    $party->save();

                    $response["error"] = false;
                    $response["msg"] = "Вы удалены из списка.";
                    return json_encode($response);
                }
            }else{

                $response["error"] = true;
                $response["error_msg"] = "Все места заняты.";
                return json_encode($response);
            }

        }else{
            $response["error"] = true;
            $response["error_msg"] = "Вписка не найдена.";
            return json_encode($response);
        }

        $response["error"] = false;
        $response['exp_access_token'] = false;
        $response["party"] = Party::with("members")->find($idParty);

        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkSetAnswer(Request $request){
        $userId = AppUser::getUserId($request->header('Access-Token'));
        $member = Member::where(['id_user' => $userId,'status' => 0])->with('party')->first();
        if(!is_null($member)){
            if(!is_null(Party::where(['created_id' => $member->id_user,'status' => 2]))){
                $response["error"] = false;
                $response['exp_access_token'] = false;
                $response["member"] = $member;

                return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
            }
        }else{
            return response()->json(null, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

    }

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
