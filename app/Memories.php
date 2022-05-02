<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Memories extends Model
{
    //
    public $table = 'tpz_memories';
    public $primaryKey="sn";
    protected $fillable =[
        'ownerid',
        'status',
        'caption',
        'duedate'
    ];
}
