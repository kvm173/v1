<?php
require_once("db.php");
session_start();


 $filename = '/tmp/action.txt';
 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);

$id_dom=$_POST["id_dom"];
$form_num=$_POST["form_num"];
$sessID=$_POST["id_sess_forpost"];
$id_prev_trans=$_POST["id_prev_trans"]; //для того чтобы удалить предыдущую трансляцию при jpg типе
$id_user=$_SESSION["id_user"];
$format_trans="JPG";
// $id_dom=1;
// $id_user=1;
  file_put_contents($filename, "--id_prev_trans -- $id_prev_trans\n", FILE_APPEND);

if ($sessID== 0)
{
      $api_acc_str=db_get_forpost_acc($id_user);

      $url="https://live.vladlink.ru/api/Login";
      $ch= curl_init($url);
      curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      // curl_setopt($ch, CURLOPT_POSTFIELDS,"Login=$api_user&Password=$api_pass");
      curl_setopt($ch, CURLOPT_POSTFIELDS,$api_acc_str);

      curl_setopt($ch, CURLOPT_HEADER, 0);
      $json_str= curl_exec($ch);
      curl_close($ch);

      $js= json_decode($json_str);
      $sessID=$js->{"SessionID"};
}

$camID=db_get_id_cam_forpost($id_dom);

$url="https://live.vladlink.ru/api/GetTranslationURL";
$ch= curl_init($url);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS,"SessionID=$sessID&CameraID=$camID&Format=HLS");
curl_setopt($ch, CURLOPT_POSTFIELDS,"SessionID=$sessID&CameraID=$camID&Format=$format_trans");

curl_setopt($ch, CURLOPT_HEADER, 0);
$json_str= curl_exec($ch);
curl_close($ch);
$js= json_decode($json_str);

// var_dump($js);

$url_tr=$js->{"URL"};

# echo "=== $url_tr ===\n";

$out_json= json_encode(array("url_forpost"=> "$url_tr","form_num"=> "$form_num", "id_sess_forpost"=> "$sessID"));
echo "$out_json";
file_put_contents($filename, "== $url_tr == \n $out_json\n", FILE_APPEND);


if ($id_prev_trans != '0' && $format_trans == "JPG") //delete old translation
{
$url="https://live.vladlink.ru/api/StopTranslation";
$ch= curl_init($url);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"SessionID=$sessID&URL=$id_prev_trans");

curl_setopt($ch, CURLOPT_HEADER, 0);
$json_str= curl_exec($ch);
curl_close($ch);
// file_put_contents($filename, "--deleted translation-- $id_prev_trans ==  $json_str\n", FILE_APPEND);
}


?>

