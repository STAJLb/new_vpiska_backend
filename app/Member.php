<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{

    public function party(){
        return $this->hasOne('App\Party','id','id_party')->select(['id','title_party','created_name'])->where('status',2);
    }
}
