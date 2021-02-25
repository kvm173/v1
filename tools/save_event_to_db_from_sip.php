<?php
require_once("db.php");
session_start();

$filename = '/tmp/action.txt';

 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);
 $date1= ("yymdHis");

 $action=$_POST["data"]["action"];
 $id=db_get_dom_from_sip($_POST["data"]["sip"]);
 $id_user=$_SESSION["id_user"];

file_put_contents($filename, "$date $action $id $id_user \n", FILE_APPEND);

 $in_json= json_encode($_POST);

 $qq= "insert into public.events (dt,id_domophon,id_user,action,event_type) values (now(),$id,$id_user,'$action',1);";
 db_insert_event($qq);
// еще запишем в форпост события от сип
// curl -s -X POST -d "AdminLogin=melnikov-admin&AdminPassword=1232021&CameraID=14284&EventID=20210111125112_02&StartRecord=10&Name=OpenDoor1" https://live.vladlink.ru/system-api/AddCustomEvent
$camID=db_get_id_cam_forpost($id);

$forpost_admin="melnikov-admin";
$forpost_admin_pass="1232021";
$url="https://live.vladlink.ru/system-api/AddCustomEvent";
$ch= curl_init($url);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS,"SessionID=$sessID&CameraID=$camID&Format=HLS");
curl_setopt($ch, CURLOPT_POSTFIELDS,"AdminLogin=$forpost_admin&AdminPassword=$forpost_admin_pass&CameraID=$camID&EventID=$date1&StartRecord=20&Name=$action");

curl_setopt($ch, CURLOPT_HEADER, 0);
$json_str= curl_exec($ch);
$resp= curl_getinfo($ch,CURLINFO_RESPONSE_CODE);

curl_close($ch);
// end еще запишем в форпост события от сип

 $out_json= json_encode(array("action"=> $action,"id"=> $id,"data"=> array("answer"=> "OK") ));
 echo  $out_json;

?>
