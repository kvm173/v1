<?php
require_once("db.php");
session_start();

$filename = '/tmp/action.txt';

 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);

 $action=$_POST["data"]["action"];
 $id=db_get_dom_from_sip($_POST["data"]["sip"]);
 $id_user=$_SESSION["id_user"];

file_put_contents($filename, "$date $action $id $id_user \n", FILE_APPEND);

 $in_json= json_encode($_POST);

 $qq= "insert into public.events (dt,id_domophon,id_user,in_json,action,event_type) values (now(),$id,$id_user,'$in_json','$action',1);";
 db_insert_event($qq);

 $out_json= json_encode(array("action"=> $action, "id"=> $id, "data"=> array("answer"=> "OK") ));
 echo  $out_json;

?>
