<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Note extends Model
{
    public static function addNote($id,$noteText){
        $note = new Note();
        $note->uid = $id;
        $note->note = $noteText;
        $note->save();
    }
}
