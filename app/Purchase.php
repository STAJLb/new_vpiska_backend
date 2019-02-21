<?php

namespace App;

use App\Http\Requests\Request;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //protected $table = 'purchases';

    public static function createPurchase($request){
        if(is_null(Purchase::where('token',$request->token)->first())){
            $purchase = new Purchase();
            $purchase->uid = AppUser::getUserId($request->access_token);
            $purchase->token  = $request->token;
            $purchase->save();

            return true;
        }
        return false;

    }
}
