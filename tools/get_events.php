<?php
require_once("db.php");
session_start();

$filename = '/tmp/action.txt';
 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);
 $id_dom=$_POST["id_dom"];
 $max_line=$_POST["max_line"];
 $id_user=$_SESSION["id_user"];

// $id_dom=1;
// $max_line=100;
// $id_user=1;
  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);
  $id=0;
  $query="SELECT dt,action,comment,et.name as name FROM events e,events_types et where id_user='$id_user' and id_domophon='$id_dom' and e.event_type=et.id order by dt desc limit $max_line;";
  $qr= pg_query($dbconn, $query);
  $res[]=array("dt","action","comment","source");
  while ($rs= pg_fetch_array($qr,NULL,PGSQL_ASSOC))
  {
    $res[]=array(substr($rs["dt"],0,19),$rs["action"],$rs["comment"],$rs["name"]);
  }


  pg_close($dbconn);

// echo $str_json;
// file_put_contents($filename, "$str_json \n", FILE_APPEND);


 $out_json= json_encode($res);
 echo "$out_json";

?>
