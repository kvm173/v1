<?php
require_once("keys_rfid_class.php");
// session_start();

$filename = '/tmp/action.txt';
 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);
// /*
$action=$_POST["action"];
$id_dom=$_POST["id_dom"];
$ip_dom=$_POST["ip_dom"];
$in_key=$_POST["in_key"];
$apart=$_POST["apart"];
// $apart=$_SESSION["appart"];
// */
/*
$action='list';
$id_dom=5;
$ip_dom='10.154.192.2';
$apart=100;
$in_key='220350076D7C3';
*/

  $kr = new Keys_Rfid($ip_dom, $apart);

  if($action=== "list")
  {
    $kr->list();
  }
  elseif($action=== "add")
  {
    $kr->add($in_key);
  }
  elseif($action=== "del")
  {
    $kr->del($in_key);
  }

?>
