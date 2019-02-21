<?php

namespace App\Http\Controllers;

use App\AppUser;
use App\Note;
use App\Party;
use Illuminate\Http\Request;
use Auth;
use Response;
use App\Http\Requests;
use App\Ban;
use Validator;
use Log;

class AdminController extends Controller
{
    public function showListAppUsers()
    {
        $appUser = AppUser::with('ban')->get();
        return view('admin.list_users')->with('user', Auth::user())->with('appUsers', $appUser);
    }

    public function showListParties()
    {
        return view('admin.list_party')->with('user', Auth::user())->with('parties', Party::all());
    }

    public function getUser(Request $request, $id)
    {
        $userApp = AppUser::with('ban', 'note')->find($id);
        return Response::json($userApp);
    }

    public function deleteBan(Request $request, $id)
    {
        AppUser::where('id', $id)->update(['status' => 0]);
        Ban::where('uid', $id)->where('status', 1)->update(['status' => 0]);

        $note = new Note();
        $note->uid = $id;
        $note->note = 'Сняты все блокировки';
        $note->save();

        $user = AppUser::find($id);
        return Response::json($user);
    }

    public function deleteUser($id)
    {
        $user = AppUser::find($id);
        $user->delete();

        $delete = true;
        return Response::json($delete);
    }

    public function showListUsers()
    {
        return view('admin.list_users')->with('users', AppUser::all())->with('user', Auth::user());
    }

    public function updateAppUser(Request $request, $id)
    {
        $user = AppUser::find($id);

        $user->first_name = $request->first_name;
        $user->nik_name = $request->nik_name;
        $user->sex = $request->sex;
        $user->status = $request->status;
        $user->balance = $request->balance;

        $user->save();

        return Response::json($user);

    }

    public function banUser(Request $request, $id)
    {
        $user = AppUser::find($id);
        $ban = Ban::where('uid', $id)->where('status', 1)->first();
        if ($ban != null) {

            $ban->status = 0;
            $ban->save();

            $user->status = 0;
            $user->save();
        }

        if ($request->type_ban == 'imei') {

            $user->status = 3;
            $user->save();

            $ban = new Ban();
            $ban->uid = $id;
            $ban->type = $request->type_ban;
            $ban->source = $request->imei;
            $ban->reason = $request->reason;
            $ban->save();
        } else {
            $user->status = 3;
            $user->save();

            $ban = new Ban();
            $ban->uid = $id;
            $ban->type = $request->type_ban;
            $ban->source = $request->nik_name;
            $ban->reason = $request->reason;
            $ban->save();
        }

        return Response::json($user);

    }

    public function addNote(Request $request, $id)
    {
        $user = AppUser::find($id);
        Note::addNote($id, $request->note);
        return Response::json($user);
    }


    //=========Вписки===================

    public function getParty($id)
    {
        $party = Party::with('user')->find($id);
        return Response::json($party);
    }

    public function updateParty(Request $request, $id)
    {
        $party = Party::find($id);

        $party->title_party = $request->title_party;
        $party->description_party = $request->description_party;
        $party->max_count_people = $request->max_count_people;
        $party->alcohol = $request->alcohol;
        $party->status = $request->status;
        $party->date_time = $request->date_time;

        $party->save();
        $party = Party::with('user')->find($id);
        return Response::json($party);

    }

    public function deleteParty($id)
    {
        $party = Party::find($id);
        $party->delete();

        $delete = true;
        return Response::json($delete);
    }

    public function createParty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'created_id' => 'bail|required',
            'title_party' => 'bail|required',
            'description_party' => 'bail|required',
            'coordinates' => 'bail|required',
            'count_people' => 'bail|required|integer|between:2,50000',
            'date_time' => 'bail|required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $createdId = $request->created_id;
        $titleParty = $request->title_party;
        $descriptionParty = $request->description_party;
        $addressParty = $request->address_party;
        $coordinates = $request->coordinates;
        $countPeople = $request->count_people;
        if ($request->alcohol == "on") {
            $alcohol = 1;
        } else {
            $alcohol = 0;
        }

        switch ($request->type_party) {
            case 'walk':
                $type = 'walk';
                break;
            case 'tea':
                $type = 'tea';
                break;
            case 'picture':
                $type = 'picture';
                break;
            case 'film':
                $type = 'film';
                break;
            case 'game':
                $type = 'game';
                break;
            case 'sacs':
                $type = 'sacs';
                break;
            default:
                $type = 'tea';
                break;
        }

        $dateTime = $request->date_time;

        $appUser = AppUser::find($createdId);

        $party = new Party();
        $party->created_id = $createdId;
        $party->title_party = $titleParty;
        $party->description_party = $descriptionParty;
        $party->created_name = $appUser->first_name . " (" . $appUser->nik_name . ")";
        $party->address = $addressParty;
        $party->coordinates = $coordinates;
        $party->max_count_people = $countPeople;
        $party->alcohol = $alcohol;
        $party->date_time = $dateTime;
        $party->type = $type;
        $party->save();

        return redirect('/admin/list-party')->with('success', "Успешно добавлено.");


    }




}
