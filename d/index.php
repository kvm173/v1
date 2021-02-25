<?php
require_once("../tools/db.php");
session_start();

$filename = '/tmp/action.txt';

 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);
// foreach ($_POST as $key => $value) {
//     $data= "{$key} => {$value}\n";
// #    print_r($arr);
//     file_put_contents($filename, $data, FILE_APPEND);
// }

 $action=$_POST["data"]["action"];
 $ip_addr=$_POST["data"]["ip_addr"]; 
 $id=$_POST["data"]["id"];
 $id_user=$_SESSION["id_user"];
 $in_json= json_encode($_POST);
 $auth=db_get_dom_acc($id);

 file_put_contents($filename, "acc==$auth==$ip_addr==$id==\n", FILE_APPEND);

 switch($action)
 {
   case "OpenDoor":
     $ch = curl_init("http://$auth@$ip_addr/cgi-bin/intercom_cgi?action=maindoor"); // such as http://example.com/example.xml
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
     curl_setopt($ch, CURLOPT_HEADER, 0);
     $data = curl_exec($ch);
     $resp=  curl_getinfo($ch,CURLINFO_RESPONSE_CODE);
     $data = str_replace(array("\r\n", "\r", "\n"), '', $data);     
     curl_close($ch);
//     echo "$data";
        if ($data === "OK")  
        { 
//          $out_json= json_encode(array("action"=> "OpenDoor", "id"=> $id, "data"=> array("answer"=> "OK") ));
          $out_json= json_encode(array("data"=>"OK","status"=>200) );
        }
        else 
        {
//          $out_json= json_encode(array("action"=> "OpenDoor", "id"=> $id, data=> array("answer"=> "ERROR") ));
          $out_json= json_encode(array("status"=>$resp,"error_message"=> $data));
        }
         echo $out_json;
         $qq= "insert into public.events (dt,id_domophon,id_user,out_json,comment,action,event_type) values (now(),$id,$id_user,'$out_json','','$action',1);";
         db_insert_event($qq);
   break;
 }

?>
