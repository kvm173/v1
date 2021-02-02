<?php

// list($type,$auth,$group,$timeout) = ('1','2','3','4');
//list($type,$timeout) = ('1','2');

echo $type;
echo $auth;
echo $group;
echo $timeout;

$v1=1;
$v2=6;
$res=ccylka($v1,$v2);
echo "$v1 $v2\n";

function ccylka($a,&$b)
{
$b=2;
$a++;


}

?>

