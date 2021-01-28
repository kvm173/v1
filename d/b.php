<?php
function db_insert_event($query)
{
  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);
  $qr= pg_query($dbconn, $query);
  pg_close($dbconn);
}
 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");

$action="OpenDoor";
$id=1;
$id_user=1;
$in_json="";
$out_json="";

$qq= "insert into public.events (dt,id_domophon,id_user,in_json,out_json,comment,action) values (now(),$id,$id_user,'$in_json','$out_json','','$action');";
db_insert_event($qq);

?>
