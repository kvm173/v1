<?php

$dd="2021-02-21 10:24";
$dd1="2021-02-19 19:30";

$dob=10* 3600;
$d=strtotime($dd)+$dob;
// $d=strtotime($dd);
echo "==$dd==$d==$dob==\n";
// curl -s -X POST -d 'AdminLogin=melnikov-admin&AdminPassword=1232021&Duration=1&TS=1613515440&TZ=10&UserLogin=melnikov-admin&UserIP=10.240.230.82' https://live.vladlink.ru/system-api/GetDownloadURL";


$api_acc_str="Login=melnikov-sb&Password=gan2dony2";
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
$SessID=$js->{"SessionID"};
echo "session=$SessID\n";

$str_par="SessionID=$SessID&CameraID=14284&Format=HLS&TS=$d&CameraTZ=0";
echo "$str_par\n";

$url="https://live.vladlink.ru/api/GetTranslationURL";
$ch= curl_init($url);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$str_par);
curl_setopt($ch, CURLOPT_HEADER,0);
$json_str= curl_exec($ch);
$resp= curl_getinfo($ch,CURLINFO_RESPONSE_CODE);

curl_close($ch);

$js= json_decode($json_str);

// var_dump($js);
if ($resp== 200)
{
 $msg=$js->{"URL"};
}
else
{
$resp=$js->{"ErrorCode"};
$msg=$js->{"Error"};
 
}
 echo "$resp $msg\n";

?>
