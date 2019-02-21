<?php

namespace App\Http\Controllers\Api;

use App\AppUser;
use App\Party;
use App\member;
use App\Report;
use App\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Log;

class PartyController extends Controller
{
    public function createParty(Request $request){


        $validator = Validator::make($request->all(), [
            'created_id' => 'bail|required',
            'title_party' => 'bail|required',
            'description_party' => 'bail|required',
            'coordinates' => 'bail|required',
            'count_people' => 'bail|required|integer|between:2,20',
            'alcohol' => 'bail|required|',
            'date_time' => 'bail|required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $response['error'] = true;
            $response['error_msg'] = $errors;
            return json_encode($response);
        }

        $createdId = $request->created_id;
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

            return json_encode($response);
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
        $party->save();

        $response["error"] = false;
        return json_encode($response);


    }

    public function  getParties(){
        $response["error"] = false;
        $response["parties"] = Party::with("members")->get();
        return json_encode($response);
    }

    public function addMembersToParty(Request $request){
        $idParty = $request->id_party;
        $idMember = $request->id_member;


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
        $response["party"] = Party::with("members")->find($idParty);

        return json_encode($response);

    }

    public function getParty($id){

        if(Party::find($id) == null){
            $response["error"] = true;
            $response["error_msg"] = "Вписка не найдена.";

            return json_encode($response);
        }
        $party = Party::with("members")->find($id);

        $idCreatedUser = $party->created_id;

        $createdUser = AppUser::find($idCreatedUser);
        $party->nik_name_created_party = $createdUser->nik_name;
        $party->rating = $createdUser->rating; 
        $response["error"] = false;
        $response["party"] = $party;


        return json_encode($response);
    }

    public function getUserCreatedParty($id){
        Log::info('Пришли данные'.$id);
            $user = AppUser::find($id);
            $response["error"] = false;
            $response["user_created_party"] = $user;
        return json_encode($response);
    }

    public function getReviewsParty($id){
        $response["error"] = false;
        $response["reviews"] = Review::where("id_party",$id)->get();
        return json_encode($response);
    }

    public function addReviewToParty(Request $request){
        $message = $request->message;
        $idParty = $request->id_party;
        $idUser = $request->id_user;

        $validator = Validator::make($request->all(), [
            'message' => 'bail|required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $response['error'] = true;
            $response['error_msg'] = $errors;
            return json_encode($response);
        }

        $user = AppUser::find($idUser);


        $review = new  Review();
        $review->id_party = $idParty;
        $review->id_user = $idUser;
        $review->message = $message;
        $review->name_user = $user->first_name." (".$user->nik_name.")";
        $review->save();

        $response["error"] = false;
        return json_encode($response);


    }

    public function createReports(Request $request){

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
            return json_encode($response);
        }

        $report = new Report();
        $report->id_reporter = $request->created_id_report;
        $report->description_report = $request->description_report;
        $report->reason = $request->reason;
        $report->id_party = $request->party_id;
        $report->save();


        $response["error"] = false;
        return json_encode($response);
    }




}
