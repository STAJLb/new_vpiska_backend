<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Firebase\JWT\JWT;
use Log;

class AppUser extends Model
{
    protected $table = 'app_users';

    public function ban()
    {
        return $this->hasMany('App\Ban','uid');
    }

    public function note(){
        return $this->hasMany('App\Note','uid');
    }

    public static function addUser($first_name,$nikName,$password, $sex,$age, $imei,$image){

        $user = new AppUser();
        $user->first_name = $first_name;
        $user->nik_name = $nikName;
        $user->password = md5($password);
        $user->sex = $sex;
        $user->imei = $imei;
        $user->image = $image;
        $user->age = $age;
       
        if($user->save()){
            return false;
        }else{
            return true;
        }
    }

    public static function createDataTokens($userId){
        $serverName = "https://clickcoffee.ru";

        $issuedAt   = time();
        $notBefore  = $issuedAt + 10;             //Adding 10 seconds
        $expireAccessToken     = $notBefore + 60;            // Adding 60 seconds


        $secretKey = ENV('APP_SECRET_KEY');

        $dataAccessToken = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expireAccessToken,           // Expire
            'data' => [                  // Data related to the signer user
                'userId'   => $userId, // userid from the users table
            ]
        ];

        $accessToken = JWT::encode(
            $dataAccessToken,      //Data to be encoded in the JWT
            $secretKey, // The signing key
            'HS256'     // Algorithm used to sign the token
        );


        $issuedAt   = time();
        $notBefore  = $issuedAt + 10;             //Adding 10 seconds
        $expireRefreshToken = $notBefore + 60*43800;

        $dataRefreshToken = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expireRefreshToken,           // Expire
            'data' => [                  // Data related to the signer user
                'refresh_flag' => true,
                'userId'   => $userId,
            ]
        ];

        $refreshToken = JWT::encode(
            $dataRefreshToken,      //Data to be encoded in the JWT
            $secretKey, // The signing key
            'HS256'     // Algorithm used to sign the token
        );

        $user = AppUser::find($userId);
        $user ->refresh_token = $refreshToken;
        $user->save();

        $resultTokens = [
          'access_token' => $accessToken,
          'exp_access_token' => $expireAccessToken,
          'refresh_token' => $refreshToken,
          'exp_refresh_token' => $expireRefreshToken
        ];

        return $resultTokens;
    }

    public static function updateDataTokens($refreshToken){
        $secretKey = ENV('APP_SECRET_KEY');
        JWT::$leeway = 60*43800+10;
        try {
            JWT::decode($refreshToken, $secretKey, array('HS256'));
        } catch (\Exception $e) { // Also tried JwtException
            switch ($e->getMessage()) {
                case 'Expired token':
                    return false;
                    break;
                case 'Signature verification failed':
                    return false;
                    break;
                case 'Wrong number of segments':
                    return false;
                    break;
            }
        }

        $dataRefreshToken =   JWT::decode($refreshToken, $secretKey, array('HS256'));

        if($dataRefreshToken->data->refresh_flag){

            $userId = $dataRefreshToken->data->userId;
            $dataTokens = self::createDataTokens($userId);

            return $dataTokens;
        }
    }





    public static function checkExitUser($nikName){
        $user = AppUser::where('nik_name', $nikName)->first();
        if ($user!=null){
            return true;
        }
        return false;
    }

    public static function checkExitImei($imei){
        $user = AppUser::where('imei', $imei)->first();
        if ($user!=null){
            return true;
        }
        return false;
    }



    public static function getInfoAboutUser($nikName){
        return AppUser::where('nik_name', $nikName)->first();
    }

    public static function checkStatus($userId){
        //0 - обычный, 1 - админ, 2 - модератор, 3 - забанен
        $user = AppUser::find($userId);
        if($user->status == 3){
            $ban = Ban::where('uid',$user->id)->where('status',1)->first();

            return $ban->reason;
        }

        return null;
    }

    public static function checkBan($userId){
        $user = AppUser::find($userId);
        $ban =  Ban::where('source',$user->imei)->where('status',1)->first();
        if( $ban  != null) {
            return $ban->reason;
        }
        $ban = Ban::where('source',$user->nik_name)->where('status',1)->first();
        if( $ban  != null) {
            return $ban->reason;
        }
        return null;
    }

    public static function checkImeiBan($userId){
        $user = AppUser::find($userId);
        $ban = Ban::where('imei_ban',$user->imei)->where('status',1)->first();
           if( $ban  != null) {
               return $ban->reason;
           }
        return null;
    }
    public static function checkImeiBanForRegister($imei){
        $ban = null;
        $ban = Ban::where('source',$imei)->where('status',1)->first();
        if( $ban  != null) {
            return $ban->reason;
        }
        return $ban;
    }

    public static function getUserLogin($nikName,$password){
        $user = AppUser::where('nik_name', $nikName)->first();
        if($user != null){
            if(md5($password) == $user->password){
                return $user;
            }
        }else{
                return null;
        }
    }

    public static function getUserId($token){

        $secretKey = ENV('APP_SECRET_KEY');
        JWT::$leeway = 1000*60*60;
        $data = JWT::decode($token, $secretKey, array('HS256'));

        return $data->data->userId;
    }


}
