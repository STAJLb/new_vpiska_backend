<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    public static function addUser($imei){

        $user = new Guest();
        $user->imei = $imei;

        if($user->save()){
            return false;
        }else{
            return true;
        }
    }


    public static function checkExitUser($imei){
        $user = Guest::where('imei', $imei)->first();
        if ($user!=null){
            return true;
        }
        return false;
    }

}
