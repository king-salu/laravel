<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use App\Http\Requests;

class eventscontroller extends Controller
{
    //gets today's upcoming events
    public function index(){
        $getImage="(SELECT tdp.name FROM tpz_data_deposit as tdp 
                    WHERE tdp.chainid=tev.sn AND tdp.chaintype=3 LIMIT 1)";
        $getLocation="(SELECT tvn.name FROM tpz_venues as tvn WHERE tvn.sn=tev.locationid)";
        $next_events =  DB::table('tpz_events as tev')
                        ->select(\DB::raw("tev.sn as id, tev.title as name, $getImage as image, 
                            tev.ownerid as eventPlannerId, $getLocation as address, tev.startime as startTime,
                            tev.endtime as endTime, tev.entrydate as creationDate"))
                        ->where([
                            ['tev.startime','<','NOW()'],
                            ['tev.status','=','1']
                        ])
                        ->orderBy('tev.startime', 'DESC')
                        ->get();

        $evnt_arr   =   json_decode(json_encode($next_events), true);
        $results= array();
        for($ev=0; $ev<count($evnt_arr); $ev++){
            $results[]=array(
                "id"=>$evnt_arr[$ev]["id"],
                "name"=>$evnt_arr[$ev]["name"],
                "address"=>$evnt_arr[$ev]["address"],
                "image"=>$evnt_arr[$ev]["image"],
                "eventPlannerId"=>$evnt_arr[$ev]["eventPlannerId"],
                "startTime"=>$evnt_arr[$ev]["startTime"],
                "endTime"=>$evnt_arr[$ev]["endTime"],
                "creationDate"=>$evnt_arr[$ev]["creationDate"]
            );
        }

        return $results;
    }
}
