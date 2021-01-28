<?php

// namespace Auth;

require_once("../tools/db.php"); 
// Проверка, залогинен ли пользователь
function isLogged() {
//    return false;
      return (isset($_SESSION["auth_token"])?true:false);
}
$id_user=1;
$in_json="";
$out_json="";
$comment="login error";
$qq= "insert into public.events (dt,id_user,in_json,out_json,comment,action) values (now(),$id_user,'$in_json','$out_json','$comment','login');";
db_insert_event($qq);

?>
