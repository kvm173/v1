<?php
require_once("../tools/db.php");
session_start();
$filename = '/tmp/login.txt';

 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);
// foreach ($_POST as $key => $value) {
//     $data= "{$key} => {$value}\n";
// #    print_r($arr);
//     file_put_contents($filename, $data, FILE_APPEND);
// }

 $in_json=json_encode($_POST);
 $login=$_POST["data"]["login"];
 $password=mb_strtoupper($_POST["data"]["password"]);
//    file_put_contents($filename, "$login $password\n", FILE_APPEND);
// $login='ko';
// $password=mb_strtoupper('b0baee9d279d34fa1dfd71aadb908c3f');

$conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
$dbconn= pg_connect($conn_string);
$qq= "select * from public.users where name='$login' and upper(pass)='$password'";
$qr= pg_query($dbconn, $qq);
$id_user=0;

  $rs= pg_fetch_array($qr,NULL,PGSQL_ASSOC);
  pg_close($dbconn);
  if ($rs) {
//    echo "more than 0 records";
    $_SESSION["auth_token"]=md5($date); $auth_token=$_SESSION["auth_token"];
    $_SESSION["login"]=$login;
    $_SESSION["id_user"]=$rs["id"]; $id_user=$rs["id"];
    $_SESSION["address"]=$rs["address"];
    $_SESSION["phone"]=$rs["phone"];
    $_SESSION["appart"]=$rs["appart"];
    $_SESSION["sip_server"]=$rs["sip_server"];
    $_SESSION["sip_user"]=$rs["sip_user"];
    $_SESSION["sip_pass"]=$rs["sip_pass"];
    $id=$rs[0];
    $comment="login success";
//    file_put_contents($filename, "more than 0 records $rs $id login=$login auth_token=$auth_token\n", FILE_APPEND);
    $out_json=json_encode(array("status"=> "200", "data"=> array("uid"=> $id, "name"=> $login, "auth_token"=> $_SESSION["auth_token"])),JSON_FORCE_OBJECT);
  }
  else {
    $comment="login error $login";

//    header("HTTP/1.0 401 Unauthorized");
    http_response_code(401);
//    file_put_contents($filename, "0 records $rs\n", FILE_APPEND);
    $out_json= json_encode(array("status"=> "401", "data"=> array("answer"=> "error")));
  }
echo $out_json;
$qq= "insert into public.events (dt,id_user,in_json,out_json,comment,action,event_type) values (now(),$id_user,'$in_json','$out_json','$comment','login',1);";
db_insert_event($qq);

?>
