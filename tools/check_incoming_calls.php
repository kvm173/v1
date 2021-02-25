<?php
// Обработка входящих пушей от домофона:
// обрабатываются ситуации:
// - action=check, просто проверка на наличие нового пуша статус=1, (статус выставляется модулем req_http.php)
// - action=update, изменение статуса пуша на "не новый"
// -- при старте приложения статус=10 для всех необработанных пушей, которые приходили когда приложение было не запущено
// -- при обработке реал.звонка статус=2 для всех звонков, которые начинались и как-то заканчивались
// -- при несостоявшемся звонке статус=3 пуш только что получен, мы приготовились принимать, ждали 10сек, но звонок не состоялся

require_once("db.php");
session_start();

$filename = '/tmp/action.txt';

$date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yy-m-d H:i:s");
     file_put_contents($filename, "$date\n", FILE_APPEND);
$action= $_POST["action"];
$id_user=$_SESSION["id_user"];

// $id_doms='1,2,4';
// $id_user=1;
// $action="update2";
// $id_dom=1;

// echo $str_json;
if ($action== "check")
{
  $id_doms=$_POST["str_domophons"];
  $qq="select id_domophon,status from public.domophons_pushs where id_domophon in (".$id_doms.") and id_user=$id_user order by id desc limit 1;";
  $rs=db_select_more_col($qq);
  $out_json= json_encode(array("id_domophon"=> $rs[0],"status"=> $rs[1]));
  echo "$out_json";
} 
elseif ($action== "update")
{
  $id_dom= $_POST["dom_call"];
  $id_update= $_POST["id_update"]; //2- end of sip session(normal or not);10- at start app; 3- no sip session 
  if ($id_update == 10) 
     { $qq="update public.domophons_pushs set status=$id_update where id_user=$id_user and status=1;"; }
  else
     { $qq="update public.domophons_pushs set status=$id_update where id_domophon =$id_dom and status=1;";}

  $rs=db_update_event($qq);
  $out_json= json_encode(array("id_domophon"=> $id_dom,"status"=> $id_update));
  echo "$out_json";
}

// file_put_contents($filename, "$out_json \n", FILE_APPEND);

?>
