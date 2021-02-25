<?php

$dd="2021-02-19 10:44";
$dd1="2021-02-19 19:30";

$dob=10* 3600;
$d=strtotime($dd)+$dob;
echo "==$d==$dob==\n";
// curl -s -X POST -d 'AdminLogin=melnikov-admin&AdminPassword=1232021&Duration=1&TS=1613515440&TZ=10&UserLogin=melnikov-admin&UserIP=10.240.230.82' https://live.vladlink.ru/system-api/GetDownloadURL";

$url="https://live.vladlink.ru/system-api/GetDownloadURL";
$str_par="AdminLogin=melnikov-admin&AdminPassword=1232021&CameraID=14284&Duration=0.27&TS=$d&TZ=0&UserLogin=melnikov-admin&UserIP=10.240.230.82&Container=mp4";
echo "$str_par\n";

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
