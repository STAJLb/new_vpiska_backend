<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use App\Member;


Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');

Route::get('/token', 'Api\TokenController@createJWT');

Route::get('/api/agreement', 'Api\UserController@showAgreement');

//Админка
Route::group(['middleware' => 'auth','prefix' => '/admin'], function () {
    Route::get('/list-users', ['uses' => 'AdminController@showListAppUsers']);
    Route::get('/list-party', ['uses' => 'AdminController@showListParties']);
    Route::get('/create/party', ['middleware' => 'admin', function () {
        return view('admin.create_party')->with('user', Auth::user());

    }]);
    Route::get('/home', ['middleware' => 'admin', function () {
        $countMembersToday = Member::where('created_at','>',\Carbon\Carbon::today())->count();
        $membersToday = Member::where('created_at','>',\Carbon\Carbon::today())->get();

        $countMembers = DB::table('members')->count();
        $members = Member::get();

        $countReviewsToday = \App\Review::where('created_at','>',\Carbon\Carbon::today())->count();
        $reviews = \App\Review::get();

        return view('admin.home')->with([
            'user'=> Auth::user(),
            'countMembersToday'=> $countMembersToday,
            'membersToday' => $membersToday,
            'countMembers' => $countMembers,
            'members' => $members,
            'countReviewsToday' => $countReviewsToday,
            'reviews' => $reviews
        ]);

    }]);
    Route::get('/users/{id?}',['uses' => 'AdminController@getUser']);

    Route::delete('/users/{id?}',['uses' => 'AdminController@deleteUser']);
    Route::put('/users/{id?}',['uses' => 'AdminController@updateAppUser']);

    Route::put('/users/note/{id?}',['uses' => 'AdminController@addNote']);
    Route::delete('/users/ban/{id?}',['uses' => 'AdminController@deleteBan']);

    Route::put('/users/ban/{id?}',['uses' => 'AdminController@banUser']);


    // Вписки
    Route::get('/parties/{id?}',['uses' => 'AdminController@getParty']);
    Route::delete('/parties/{id?}',['uses' => 'AdminController@deleteParty']);
    Route::put('/parties/{id?}',['uses' => 'AdminController@updateParty']);

    Route::post('/parties/create',['uses'=>'AdminController@createParty']);
});


////Админка v2
//Route::group(['middleware' => 'auth','prefix' => '/admin/new'], function () {
//    Route::get('/list-users', ['uses' => 'AdminController@showListAppUsers']);
//    Route::get('/list-party', ['uses' => 'AdminController@showListParties']);
//    Route::get('/create/party', ['middleware' => 'admin', function () {
//        return view('admin.create_party')->with('user', Auth::user());
//
//    }]);
//    Route::get('/home', ['middleware' => 'admin', function () {
//        $countMembersToday = Member::where('created_at','>',\Carbon\Carbon::today())->count();
//
//        return view('admin.home')->with(['user'=> Auth::user(),'countMembersToday'=> $countMembersToday]);
//
//    }]);
//    Route::get('/users/{id?}',['uses' => 'AdminController@getUser']);
//
//    Route::delete('/users/{id?}',['uses' => 'AdminController@deleteUser']);
//    Route::put('/users/{id?}',['uses' => 'AdminController@updateAppUser']);
//
//    Route::put('/users/note/{id?}',['uses' => 'AdminController@addNote']);
//    Route::delete('/users/ban/{id?}',['uses' => 'AdminController@deleteBan']);
//
//    Route::put('/users/ban/{id?}',['uses' => 'AdminController@banUser']);
//
//
//    // Вписки
//    Route::get('/parties/{id?}',['uses' => 'AdminController@getParty']);
//    Route::delete('/parties/{id?}',['uses' => 'AdminController@deleteParty']);
//    Route::put('/parties/{id?}',['uses' => 'AdminController@updateParty']);
//
//    Route::post('/parties/create',['uses'=>'AdminController@createParty']);
//});

//Route::get('/admin/test-parser',['uses' => 'AdminController@testParser']);





//=========================ВЕРСИЯ API 1==============================================

Route::group(['middleware'=>'token','namespace' => 'Api\v1','prefix' => '/api/v1/users' ], function (){

    Route::post('/login',['uses'=>'UserController@loginUser']);
    Route::post('/update',['uses'=>'UserController@updateUser']);
    Route::post('/update/image',['uses'=>'UserController@updateAvatar']);
    Route::get('/rating/{access_token?}',['uses'=>'UserController@getRatingTable']);
    Route::get('/profile/{access_token?}',['uses'=>'UserController@getMyProfile']);

    Route::get('/{id?}/{access_token?}',['uses'=>'PartyController@getUserCreatedParty']);

    Route::put('/update/rating',['uses'=>'UserController@updateRatingUser']);
    Route::put('/notes',['uses'=>'UserController@updateStatusNote']);
    Route::get('/check-update-rating/{access_token?}',['uses'=>'UserController@checkUpdateRating']);




});
Route::post('/api/v1/users/login',['uses'=>'Api\v1\UserController@loginUser']);
Route::post('/api/v1/users/register',['uses'=>'Api\v1\UserController@registerUser']);
Route::put('/api/v1/users/token/update',['uses'=>'Api\v1\UserController@updateDataTokens']);


Route::group(['middleware'=>'token','namespace' => 'Api\v1','prefix' => '/api/v1/parties' ], function (){
    Route::post('/create',['uses'=>'PartyController@createParty']);
    Route::get('/{access_token?}/',['uses'=>'PartyController@getParties']);
    Route::post('/reviews/add',['uses'=>'PartyController@addReviewToParty']);
    Route::get('/reviews/{id?}/{access_token?}',['uses'=>'PartyController@getReviewsParty']);
    Route::post('/reports',['uses'=>'PartyController@createReports']);
    Route::post('/members',['uses' => 'PartyController@addMembersToParty']);
});
Route::group(['middleware'=>'token','namespace' => 'Api\v1','prefix' => '/api/v1/party' ], function (){
    Route::get('/{id?}/{access_token?}/',['uses' => 'PartyController@getParty']);
});


//=========================ВЕРСИЯ API 2==============================================


//UserController
Route::group(['middleware'=>['token','version'],'namespace' => 'Api\v2','prefix' => '/api/v2/users' ], function (){

    Route::get('/profile',['uses'=>'UserController@show']);
    Route::post('/feedback',['uses'=>'FeedbackController@store']);
    Route::post('/update',['uses'=>'UserController@update']);
    Route::post('/update/balance',['uses'=>'UserController@updateBalance']);
    Route::post('/update/number-view',['uses'=>'UserController@buyView']);
    Route::post('/update/number-adding',['uses'=>'UserController@buyAdding']);


});

//NoteController
Route::group(['middleware'=>['token','version'],'namespace' => 'Api\v2','prefix' => '/api/v2/notes' ], function (){
    Route::put('/update/{id?}',['uses'=>'NoteController@update']);
});

//RatingController
Route::group(['middleware'=>['token','version'],'namespace' => 'Api\v2','prefix' => '/api/v2/ratings' ], function (){
    Route::get('/',['uses'=>'RatingController@index']);
    Route::put('/update/{id?}',['uses'=>'RatingController@update']);
    Route::get('/check-update-rating',['uses'=>'RatingController@checkUpdateRating']);
});

//PartyController
Route::group(['middleware'=>['token','version'],'namespace' => 'Api\v2','prefix' => '/api/v2/parties' ], function (){
    Route::get('/',['uses'=>'PartyController@index']);
    Route::get('/{id?}',['uses' => 'PartyController@show']);
    Route::post('',['uses'=>'PartyController@store']);


});
//ReviewController
Route::group(['middleware'=>['token','version'],'namespace' => 'Api\v2','prefix' => '/api/v2/reviews' ], function (){
    Route::get('/{id?}',['uses'=>'ReviewController@show']);
    Route::post('',['uses'=>'ReviewController@store']);
});
//TokenController 
Route::group(['namespace' => 'Api\v2','prefix' => '/api/v2/tokens' ], function (){
    Route::put('/update',['uses'=>'TokenController@update']);
});
//AvatarController
Route::group(['namespace' => 'Api\v2','prefix' => '/api/v2/avatars' ], function (){
    Route::post('/update/image',['uses'=>'AvatarController@update']);
});
//ReportController
Route::group(['middleware'=>['token','version'],'namespace' => 'Api\v2','prefix' => '/api/v2/reports' ], function (){
    Route::post('/',['uses' => 'ReportController@store']);
});
//MemberController
Route::group(['middleware'=>['token','version'],'namespace' => 'Api\v2','prefix' => '/api/v2/members' ], function (){
    Route::post('',['uses' => 'MemberController@store']);
    Route::get('/check-set-answer',['uses'=>'MemberController@checkSetAnswer']);
});

//PurchaseController
Route::group(['middleware'=>['token','version'],'namespace' => 'Api\v2','prefix' => '/api/v2/purchases' ], function (){
    Route::post('',['uses' => 'PurchaseController@store']);
});

//Auth
Route::post('/api/v2/users/login',['uses'=>'Api\v2\UserController@loginUser']);
Route::post('/api/v2/users/register',['uses'=>'Api\v2\UserController@create']);

Route::get('/test',['uses'=>'AdminController@test']);
Route::get('/test1',['uses'=>'AdminController@test1']);






