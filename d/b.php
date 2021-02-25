<?php
 $date= (new DateTime('NOW', new DateTimeZone('Asia/Vladivostok')))->format("yymdHis");
     file_put_contents($filename, "$date\n", FILE_APPEND);

echo "$date\n";
$dd=date("yymdHis");
echo "$dd\n";

?>
