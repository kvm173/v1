<?php

// Для простых операций: открыть основную дверь, дополнительную дверь
const SYSLOG_SERVER= "10.154.192.23";
const SYSLOG_SERVER_PORT= 514;
const SYSLOG_ENABLE= true;

Class Doors
{

  protected $ip_dom;
  protected $auth;
  protected $door;

  public $out_json;  

        function __construct($ip_dom,$auth)
        {
          $this->ip_dom= $ip_dom;
          $this->auth= $auth;
        }
       
        public function open_door($type_door) //maindoor,altdoor
        {
          $this->door= $type_door;

if(SYSLOG_ENABLE) { send_remote_syslog("open the door from".$this->ip_dom." type ".$this->door); }

          // $ch = curl_init("http://$auth@$ip_addr/cgi-bin/intercom_cgi?action=maindoor");
          $url="http://".$this->auth."@".$this->ip_dom."/cgi-bin/intercom_cgi?action=".$this->door;
          $resp=$this->get_curl($url,$msg);
              if ($msg === "OK")  
              { 
                $this->out_json= json_encode(array("data"=>"OK","status"=>200) );
              }
              else 
              {
                $this->out_json= json_encode(array("status"=>$resp,"error_message"=> $msg));
              }

           echo $this->out_json;
        }


        private function get_curl($uu,&$msg)
        {

           $ch = curl_init($uu);
           curl_setopt($ch, CURLOPT_HEADER, 0);
           curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
           curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

           $msg= curl_exec($ch);
           $msg = str_replace(array("\r\n", "\r", "\n"), '', $msg); 
           $resp= curl_getinfo($ch,CURLINFO_RESPONSE_CODE);

           curl_close($ch);

          return $resp;
        }

}

function send_remote_syslog($message, $component = "api_domophon", $program = "php_domophon_system_api") {
  $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
  foreach(explode("\n", $message) as $line) {
    $syslog_message = date('M d H:i:s ') . $program . ' ' . $component . ': ' . $line;
    socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, SYSLOG_SERVER, SYSLOG_SERVER_PORT);
  }
  socket_close($sock);
}


?>