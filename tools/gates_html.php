<?php
session_start();

require_once("gates_class.php");
require_once("db.php");
$filename = '/tmp/action1.txt';
$date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
// $date=date('yy-m-d H:i:s',time());
     file_put_contents($filename, "$date gates\n", FILE_APPEND);
$id_user=$_SESSION["id_user"];
$id_gate=$_POST["id_gate"];
// $id_gate='1';
// $id_user='1';

$qq="select ip_addr,type,convert_from(decode(gate_pass,'base64'),'UTF-8'),timeout,out_group from gates where id=$id_gate;";
$rs=db_select_more_col($qq);

$ip_gate=$rs[0];
$type=   $rs[1];
$pass=   $rs[2];
$timeout=$rs[3];
$group=  $rs[4];

  $cg= new Gates($ip_gate, $type, $pass, $timeout);
  $cg->ele_impuls($group);

  $out=$cg->out_json;
 
  $qq= "insert into public.events (dt,id_gate,id_user,action,out_json,comment,event_type) values (now(),$id_gate,$id_user,'OpenCloseGate','$out','$out',3);";
  db_insert_event($qq);

?>