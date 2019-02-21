<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    public function user(){
        return $this->hasOne('App\AppUser','id','created_id');
    }
    public function member(){
        return $this->hasMany('App\Member','id_party','id');
    }

}
