<?php

namespace App\Http\Controllers;

use File;

Use App\Memories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Http\Requests;



class MemoriesControl extends Controller
{
    //
    public function index(){
        //return Memories::all();
    }

    public function show($memory){
        $mem = DB::table('tpz_memories')->where('sn',$memory)
                                        ->orderby('duedate')
                                        ->get();
        return $mem;
        //return Memories
        //return Memories::find($memory);
    }

    public function getMemories(){
        $subquery="SELECT COUNT(*) FROM tpz_memories_activ as tma WHERE tma.engaged=tm.ownerid AND tma.memoid=tm.sn";
        
        //DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        $mem_tab = DB::table('tpz_memories as tm')
                    ->select(\DB::raw("tm.sn, tdp.name as file, tdp.datatype, tm.ownerid as userid, tm.entrydate,
                                        tm.caption,
                            (SELECT username FROM tpz_profile WHERE sn=tm.ownerid) as username,
                            (SELECT firstname FROM tpz_profile WHERE sn=tm.ownerid) as firstname,
                            (SELECT lastname FROM tpz_profile WHERE sn=tm.ownerid) as lastname,
                            (SELECT tdp2.name FROM tpz_data_deposit as tdp2
                                WHERE tdp2.chainid=tm.ownerid AND tdp2.chaintype=0)
                            as avartar,
                            ($subquery) as engagements"))
                    ->join('tpz_data_deposit as tdp','tdp.chainid','=','tm.sn')
                    ->where('tdp.chaintype','=','1')
                    ->orderBy('engagements', 'DESC')
                    ->take(20)
                    ->get();
        $mem_tabA= json_decode(json_encode($mem_tab), true);
        $results= array();
        for($m=0; $m<count($mem_tabA); $m++){
            $results[]=array(
                "userid"=>$mem_tabA[$m]["userid"],
                "name"=>$mem_tabA[$m]["firstname"]." ".$mem_tabA[$m]["lastname"],
                "avartar"=>$mem_tabA[$m]["avartar"],
                "count"=>$mem_tabA[$m]["engagements"],
                "status"=>  array(
                               "id"=>$mem_tabA[$m]["sn"],
                               "file"=>$mem_tabA[$m]["file"],
                               "caption" =>$mem_tabA[$m]["caption"],
                               "created"=>$mem_tabA[$m]["entrydate"]
                            )
                );
        }
        
        $newList =           json_encode($results);

        FILE::put(public_path().'/memsamp1.json', $newList);
        //return response()->download(public_path('memsamp1.json'));

        //dd(__)
        return $results;
        //return dd(DB::getQueryLog());
    }

    public function NewMemo(){
        return Memories::all();
    }
}
