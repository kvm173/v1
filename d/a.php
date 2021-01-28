<?php
    $ip_addr="10.154.192.22";
    $auth="admin:admin";
    $str="http://$auth@$ip_addr/cgi-bin/intercom_cgi?action=maindoor";
    echo "$str\n";
     $ch = curl_init($str); // such as http://example.com/example.xml
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_HEADER, 0);
     $data = curl_exec($ch);
     $data = str_replace(array("\r\n", "\r", "\n"), '', $data);
    
     echo "==$data==";
     curl_close($ch);

?>
