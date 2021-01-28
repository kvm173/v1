<?php
require_once("db.php");
session_start();

$filename = '/tmp/action.txt';
 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);
// $dom_id=1;
 $dom_id=$_POST["id_dom"];
 $form_num=$_POST["form_num"];
 $str_json=db_get_dom_auth_ip($dom_id);

// echo $str_json;
// file_put_contents($filename, "$str_json \n", FILE_APPEND);
$j=json_decode($str_json);
$ip= $j->{"ip_addr"}; $user= $j->{"dom_user"}; $pass= base64_decode($j->{"dom_pass"});

$r=rand(1000000,9999999);
$url="http://$user:$pass@$ip/cgi-bin/images_cgi?channel=0&0.$r";

// */
$ch= curl_init($url);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);

$fp = fopen("../../tmp/pic/image$dom_id.jpg", "wb");
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);
$noerr= curl_exec($ch);
curl_close($ch);
fclose($fp);
// */

 $out_json= json_encode(array("noerr"=> "$noerr","form_num"=> "$form_num"));
 echo "$out_json";

?>
