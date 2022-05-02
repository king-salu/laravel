<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\DB;

class inboxControl extends Controller
{
    //
    public function getInbox($owner){
        $results= array();
        $inbox_tab= DB::table('tpz_direct_messages')
                    ->select("recipientid")->distinct()
                    ->where('ownerid','=',$owner)
                    ->orWhere('recipientid','=',$owner)
                    ->get();

        $recipientList=json_decode(json_encode($inbox_tab), true);   
        for($r=0; $r<count($recipientList); $r++){
            $Receiver=$recipientList[$r]['recipientid'];
            $inbox_tab=DB::table('tpz_direct_messages AS dm')
                                ->select(\DB::raw("dm.ownerid, dm.message, dm.dateseen, dm.entrydate, dm.recipientid, 
                                                (SELECT lastname FROM tpz_profile WHERE sn=dm.recipientid) as recipient_lastname,
                                                (SELECT firstname FROM tpz_profile WHERE sn=dm.recipientid) as recipient_firstname, 
                                                CASE dm.status
                                                    WHEN 0 THEN 'sent'
                                                    WHEN 1 THEN 'read'
                                                    ELSE dm.status
                                                END as status"))
                                ->where('dm.status','<>','2') 
                                ->where(function($query) use ($owner,$Receiver){
                                    $query->where([['dm.recipientid','=',$Receiver],
                                                    ['dm.ownerid','=',$owner]])
                                          ->orWhere([['dm.recipientid','=',$owner],
                                                    ['dm.ownerid','=',$Receiver]]);
                                })
                                ->orderBy('dm.entrydate','ASC')
                                ->get();

            $Messages=json_decode(json_encode($inbox_tab), true);
            $Msg_thread=array();
            $R_name=(count($Messages)>0)?$Messages[0]['recipient_lastname'].' '.$Messages[0]['recipient_firstname']:"";
            for($m=0; $m<count($Messages); $m++){
                $Msg_thread[]=array(
                    "from_id"=>($Messages[$m]['ownerid']==$owner)?"SELF":$Messages[$m]['ownerid'],
                    "to_id"=>($Messages[$m]['recipientid']==$owner)?"SELF":$Messages[$m]['recipientid'],
                    "from"=>($Messages[$m]['ownerid']==$owner)?"SELF":$Messages[$m]['recipient_lastname'].' '.$Messages[$m]['recipient_firstname'],
                    "to"=>($Messages[$m]['recipientid']==$owner)?"SELF":$Messages[$m]['recipient_lastname'].' '.$Messages[$m]['recipient_firstname'],
                    "message"=>$Messages[$m]['message'],
                    "date_sent"=>$Messages[$m]['entrydate'],
                    "date_seen"=>$Messages[$m]['dateseen'],
                    "status"=>$Messages[$m]['status'],
                );
            }
            
            if($R_name!=""){
                $results[]=array(
                    "recipient"=>$R_name,
                    "preview_msg"=>(count($Msg_thread)>0)?$Msg_thread[0]['message']:"",
                    "messages"=>$Msg_thread
                );
            }
            
        }
        return $results;
    }
}
