<?php

namespace App\Http\Controllers\Api\v2;

use App\AppUser;
use App\Member;
use App\Party;

use App\Report;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Log;



class PartyController extends Controller
{

    public function index(Request $request){
        $token = $request->header('Access-Token');
        $response["error"] = false;
        $response['exp_access_token'] = false;
     //   $response["user_id"] = AppUser::getUserId($token);
        $response["data_user"] = AppUser::select(['count_view','max_number_view','count_adding_party','max_number_adding_party','balance'])->where('id',AppUser::getUserId($token))->first();


        $response["parties"] = Party::where('status',1)->get();
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request){

        Log::debug($request);
        $validator = Validator::make($request->all(), [
            'title_party' => 'bail|required',
            'description_party' => 'bail|required',
            'coordinates' => 'bail|required',
            'count_people' => 'bail|required|integer|between:2,20',
            'alcohol' => 'bail|required|',
            'date_time' => 'bail|required',
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
        $createdId = AppUser::getUserId($token);
        $titleParty = $request->title_party;
        $descriptionParty = $request->description_party;
        $addressParty = $request->address_party;
        $coordinates = $request->coordinates;
        $countPeople = $request->count_people;
        $alcohol = $request->alcohol;
        $dateTime = $request->date_time;

        $appUser = AppUser::find($createdId);

        if(Party::where('created_id',$createdId)->where('status',1)->count() >= 3){
            $response["error"] = true;
            $response["error_msg"] = "Лимит создания вписок превышен (не более двух).";

            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        if($appUser->count_adding_party == $appUser->max_number_adding_party){
            if($appUser->balance >= 100){
                $appUser->balance = $appUser->balance - 100;
                $appUser->save();
                switch ($request->type_party){
                    case "0":
                        $type = 'walk';
                        break;
                    case "1":
                        $type = 'tea';
                        break;
                    case "2":
                        $type = 'picture';
                        break;
                    case "3":
                        $type = 'film';
                        break;
                    case "4":
                        $type = 'game';
                        break;
                    case "5":
                        $type = 'sacs';
                        break;
                    default:
                        $type = 'tea';
                        break;
                }

                $party = new Party();
                $party->created_id = $createdId;
                $party->title_party = $titleParty;
                $party->description_party = $descriptionParty;
                $party->created_name = $appUser->first_name." (".$appUser->nik_name.")";
                $party->address = $addressParty;
                $party->coordinates = $coordinates;
                $party->max_count_people = $countPeople;
                $party->alcohol = $alcohol;
                $party->date_time = $dateTime;
                $party->type = $type;
                $party->save();

                $response["error"] = false;

                return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
            }else{
                $response["error"] = true;
                $response["max_number_view"] = true;
                $response["error_msg"] = "Лимит просмотров исчерпан.";

                return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
            }

        }else{
            $appUser->count_adding_party = $appUser->count_adding_party + 1;
            $appUser->save();
            switch ($request->type_party){
                    case "0":
                        $type = 'walk';
                        break;
                    case "1":
                        $type = 'tea';
                        break;
                    case "2":
                        $type = 'picture';
                        break;
                    case "3":
                        $type = 'film';
                        break;
                    case "4":
                        $type = 'game';
                        break;
                    case "5":
                        $type = 'sacs';
                        break;
                    default:
                        $type = 'tea';
                        break;
                }

                $party = new Party();
                $party->created_id = $createdId;
                $party->title_party = $titleParty;
                $party->description_party = $descriptionParty;
                $party->created_name = $appUser->first_name." (".$appUser->nik_name.")";
                $party->address = $addressParty;
                $party->coordinates = $coordinates;
                $party->max_count_people = $countPeople;
                $party->alcohol = $alcohol;
                $party->date_time = $dateTime;
                $party->type = $type;
                $party->save();

                $response["error"] = false;

                return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }




    }


    public function show(Request $request,$id){
        $token = $request->header('Access-Token');

        $response['exp_access_token'] = false;
        $response["max_number_view"] = false;

        $idUser = AppUser::getUserId($token);

        $appUser = AppUser::find($idUser);
        $party = Party::with("member")->find($id);

        $idCreatedUser = $party->created_id;

        $createdUser = AppUser::find($idCreatedUser);
        $party->nik_name_created_party = $createdUser->nik_name;
        $party->rating = $createdUser->rating;
        $response["error"] = false;
        $response["id_user"] = $idUser;
        $response["party"] = $party;

        if(!is_null(Member::where(['id_user'=>$idUser,'id_party'=>$id])->first())){

            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }else if($appUser->count_view == $appUser->max_number_view){

                $response["error"] = true;
                $response["max_number_view"] = true;
                $response["error_msg"] = "Лимит просмотров исчерпан.";

                return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);

        }else{
            $appUser->count_view = $appUser->count_view + 1;
            $appUser->save();

           return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

    }

    public function getUserCreatedParty($id){

        $response['exp_access_token'] = false;
            $user = AppUser::find($id);
            $response["error"] = false;
            $response["user_created_party"] = $user;
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }




    public function createReports(Request $request){
        $response['exp_access_token'] = false;

        $validator = Validator::make($request->all(), [
            'created_id_report' => 'bail|required',
            'description_report' => 'bail|required',
            'reason' => 'bail|required',
            'party_id' => 'bail|required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $response['error'] = true;
            $response['error_msg'] = $errors;
            return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
        }

        $report = new Report();
        $report->id_reporter = $request->created_id_report;
        $report->description_report = $request->description_report;
        $report->reason = $request->reason;
        $report->id_party = $request->party_id;
        $report->save();


        $response["error"] = false;
        return response()->json($response, 200,array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
    }

}
