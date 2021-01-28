<?php

function db_insert_event($query)
{
  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);
  $qr= pg_query($dbconn, $query);
  pg_close($dbconn);
}

function db_get_dom_acc($id)
{
  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);

  $query="SELECT dom_user || ':' || convert_from(decode(dom_pass,'base64'),'UTF-8') FROM public.domophons where id=$id;";
  $qr= pg_query($dbconn, $query);
  $rs= pg_fetch_array($qr,NULL,PGSQL_NUM);
  pg_close($dbconn);
  return $rs[0];
}

function db_get_dom_acc_by_ip($ip)
{
  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);

  $query="SELECT dom_user || ':' || convert_from(decode(dom_pass,'base64'),'UTF-8') FROM public.domophons where ip_addr='$ip';";
  $qr= pg_query($dbconn, $query);
  $rs= pg_fetch_array($qr,NULL,PGSQL_NUM);
  pg_close($dbconn);
  return $rs[0];
}

function db_get_dom_from_sip($sip)
{
  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);
  $id=0;
  $query="SELECT id FROM public.domophons where sip_user='$sip';";
  $qr= pg_query($dbconn, $query);
  $rs= pg_fetch_array($qr,NULL,PGSQL_NUM); if ($rs) { $id=$rs[0];}
  pg_close($dbconn);
  return $id;
}

function db_get_dom_auth_ip($id)
{
  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);
  $query="SELECT ip_addr,dom_user,dom_pass FROM public.domophons where id=$id;";
  $qr= pg_query($dbconn, $query);
  $rs= pg_fetch_array($qr,NULL,PGSQL_NUM); if ($rs) { $ip_addr=$rs[0]; $dom_user=$rs[1]; $dom_pass=$rs[2]; }
  pg_close($dbconn);

  $str_json=json_encode(array("ip_addr"=> $ip_addr, "dom_user"=> $dom_user, "dom_pass"=> $dom_pass));
  return $str_json;

}

function db_get_forpost_acc($id)
{
  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);

  $query="SELECT 'Login=' || forpost_user || '&Password=' || convert_from(decode(forpost_pass,'base64'),'UTF-8') FROM public.users where id=$id";
  $qr= pg_query($dbconn, $query);
  $rs= pg_fetch_array($qr,NULL,PGSQL_NUM);
  pg_close($dbconn);
  return $rs[0];
}

function db_get_id_cam_forpost($id)
{
  $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
  $dbconn= pg_connect($conn_string);

  $query="SELECT id_forpost FROM public.domophons where id=$id;";
  $qr= pg_query($dbconn, $query);
  $rs= pg_fetch_array($qr,NULL,PGSQL_NUM);
  pg_close($dbconn);
  return $rs[0];
}

?>
