<?php
    $ip_addr='10.154.192.17';
    $pass="public";
    $url="http://$ip_addr/checkpassword.cgi?psw_check=$pass";
    $group=5; $group--;
    $fcookie="/tmp/cook$ip_addr" . "_$group";
    echo "$url\n$fcookie\n";

    $ch = curl_init($url); //$
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $fcookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $fcookie);

    $data = curl_exec($ch);
    curl_close($ch);

echo "$data\n";

    $url="http://$ip_addr/outputs.cgi?outp".$group."type=1&outp".$group."t=3&reload".$group."=on";
echo "==$url==\n==$fcookie==\n";
    $ch = curl_init($url); //
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $fcookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $fcookie);

    $data = curl_exec($ch);
echo "$data\n";
    curl_close($ch);


?>
