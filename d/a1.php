<?php


    $ip_addr='10.154.192.18';
    $type="";$auth="";$group="";$timeout="";
//    $group=2;
//    $timeout=3;
echo "== $type $auth $group $timeout ==\n";
    get_gates_par_by_ip($ip_addr);
echo "++ $type $auth $group $timeout ++\n";

    $str_outp= ($group==2 ? "2" : "");
    $url="http://$ip_addr/d_ts=1?outp$str_outp=on&pw=$auth&fd=1";

//    $group=1; $group--;
//    $fcookie="/tmp/cook$ip_addr" . "_$group";
    echo "$url\n$fcookie\n";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
#    curl_setopt($ch, CURLOPT_COOKIEJAR, $fcookie);
#    curl_setopt($ch, CURLOPT_COOKIEFILE, $fcookie);

    $data = curl_exec($ch);
    $resp=curl_getinfo($ch,CURLINFO_RESPONSE_CODE);

    curl_close($ch);
echo "resp=$resp\n";

# echo "$data\n";
    if ($group == 2)
    {
    sleep($timeout);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
#    curl_setopt($ch, CURLOPT_COOKIEJAR, $fcookie);
#    curl_setopt($ch, CURLOPT_COOKIEFILE, $fcookie);

    $data = curl_exec($ch);
    $resp=curl_getinfo($ch,CURLINFO_RESPONSE_CODE);
    curl_close($ch);

echo "resp=$resp\n";
echo "data=$data\n";
    }

function get_gates_par_by_ip($ip)
{
global $type,$auth,$group,$timeout;

  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);

  $query= "SELECT type,convert_from(decode(gate_pass,'base64'),'UTF-8'),out_group,timeout FROM gates where ip_addr='$ip';";
  $qr= pg_query($dbconn, $query);
  $rs= pg_fetch_array($qr,NULL,PGSQL_NUM);
  pg_close($dbconn);

  $type= $rs[0];$auth= $rs[1]; $group= $rs[2]; $timeout=$rs[3];

  return;

}


?>

