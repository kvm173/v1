<?php
require_once("gates_class.php");

// $filename = '/tmp/action.txt';
// $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
// $date=date('yy-m-d H:i:s',time());
//     file_put_contents($filename, "$date\n", FILE_APPEND);
/*
$ip_gate=$_POST["ip_gate"];
$type=$_POST["type"];   // 1= SNR-ERD-PROject2; 2= SNR-ERD-2.0
$pass=$_POST["pass"];
$timeout=$_POST["timeout"];
$group=$_POST["group"]; // группа контактов для импульса замыкания-размыкания
*/
// /*
$ip_gate='10.154.192.17';
$type=1;  // 1= SNR-ERD-PROject2; 2= SNR-ERD-2.0
$pass='public';
$timeout=2;
$group=6; // группа контактов для импульса замыкания-размыкания
// */

  $cg= new Gates($ip_gate, $type, $pass, $timeout);
  $cg->ele_impuls($group);

?>
