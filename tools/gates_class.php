<?php

// Для работы со шлагбаумом, умеет работать с двумя типами контроллеров
// SNR-ERD-2.0, SN-ERD-PROject2
define("SYSLOG_SERVER","10.154.192.23");
define("SYSLOG_SERVER_PORT",514);
define("SYSLOG_ENABLE",true);

Class Gates
{
  protected $ip_gate;
  protected $type;
  protected $pass;
  protected $group; // электрическая группа контатов присоединенная на разъеме контроллера  1...[max]
  protected $timeout;

        function __construct($ip_gate,$type,$pass,$timeout=3)
        {
          $this->ip_gate=$ip_gate;
          $this->type=$type;
          $this->pass=$pass;
          $this->timeout=$timeout;
        }

        public function ele_impuls($group)
        {
          $this->group=$group;
          $msg="";

if(SYSLOG_ENABLE)
{
send_remote_syslog("sent signal to ".$this->ip_gate." type ".$this->type);
}
          if($this->type== 1) // SNR-ERD-PROject2
          {
            $url="http://".$this->ip_gate."/checkpassword.cgi?psw_check=".$this->pass;
            $cookie="/tmp/cookie_".$this->ip_gate;
            $rcurl=$this->get_curl($url,$msg,$cookie);
//            echo "1++ rcurl=$rcurl\nmsg=$msg\n";
              if ($rcurl== 200)
              {
                if (strpos($msg,"<body onload=\"loadMenu") !== false)
                {
                  $url_group=$this->group-1;
                  $url="http://".$this->ip_gate."/outputs.cgi?outp".$url_group."type=1&outp".$url_group."t=".$this->timeout."&reload".$url_group."=on";
                  $rcurl=$this->get_curl($url,$msg,$cookie);
                  if (strpos($msg,"<body onload=\"loadMenu") === false) { $rcurl=401; }
//            echo "2++ rcurl=$rcurl\nmsg=$msg\n";
                }
                else { $rcurl=401; }
              }
              if(file_exists($cookie)) { unlink($cookie); }
          }

          elseif($this->type== 2) // SNR-ERD-2.0
          {
            $str_outp= ($group==2 ? "2" : "");
            $url="http://".$this->ip_gate."/d_ts=1?outp$str_outp=on&pw=".$this->pass."&fd=1";
            $rcurl=$this->get_curl($url,$msg);
//            echo "1-- rcurl=$rcurl\nmsg=$msg\n";
            if (strpos($msg,"Now is ") === false) { $rcurl=401; }

            if ($rcurl== 200 && $group== 2)
            {
               sleep($this->timeout);
               $rcurl=$this->get_curl($url,$msg);
               if (strpos($msg,"Now is ") === false) { $rcurl=401; }
            }

          }


          if ($rcurl== 200)
          {
             $out_json= json_encode(array("data"=>"OK","status"=>200));
          }
          else
          {
             $out_json= json_encode(array("status"=>$rcurl,"error_message"=> $msg));
          }

          echo "$out_json";

        }

        private function get_curl($uu,&$msg,$fcookie='')
        {

           $ch = curl_init($uu);
           curl_setopt($ch, CURLOPT_HEADER, 1);
           curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
           curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

           if($this->type== 1)
           {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $fcookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $fcookie);
           }

           $msg= curl_exec($ch);
           $resp= curl_getinfo($ch,CURLINFO_RESPONSE_CODE);

           curl_close($ch);

          return $resp;
        }

}

function send_remote_syslog($message, $component = "api_gate", $program = "php_domophon_system_api") {
  $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
  foreach(explode("\n", $message) as $line) {
    $syslog_message = date('M d H:i:s ') . $program . ' ' . $component . ': ' . $line;
    socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, SYSLOG_SERVER, SYSLOG_SERVER_PORT);
  }
  socket_close($sock);
}

