<?php
require_once("db.php");
session_start();

$filename = '/tmp/action.txt';

 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);

$id=db_get_dom_from_sip($_POST["data"]["sip"]);
// $id=1;
file_put_contents($filename, "$date $action $id $id_user \n", FILE_APPEND);

 $out_json= json_encode(array("id_dom"=> $id ));
 echo  $out_json;

?>
