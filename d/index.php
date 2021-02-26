<?php
require_once("../tools/db.php");
require_once("doors_class.php");

session_start();

// $filename = '/tmp/action.txt';

// $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
//     file_put_contents($filename, "$date\n", FILE_APPEND);
//  $in_json= json_encode($_POST);

 $action=$_POST["data"]["action"];
 $ip_addr=$_POST["data"]["ip_addr"]; 
 $id=$_POST["data"]["id"];
 $id_user=$_SESSION["id_user"];
/*
 $action="OpenDoorDop";
 $ip_addr="10.154.192.21"; 
 $id="1";
 $id_user="1";
*/
 $auth=db_get_dom_acc($id);

//  file_put_contents($filename, "acc==$auth==$ip_addr==$id==\n", FILE_APPEND);
  $cg= new Doors($ip_addr,$auth);

 switch($action)
 {
   case "OpenDoor":
    $cg->open_door("maindoor");
    break;

   case "OpenDoorDop":
    $cg->open_door("altdoor");
    break;

 }

     $qq= "insert into public.events (dt,id_domophon,id_user,out_json,comment,action,event_type) values (now(),$id,$id_user,'$cg->out_json','$cg->out_json','$action',1);";
     db_insert_event($qq);

?>
