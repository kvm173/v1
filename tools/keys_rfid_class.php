<?php

Class Keys_Rfid
{
  protected $ip_dom;
  protected $apart;
  protected $acc;

        function __construct($ip_dom,$apart)
        {
          $this->ip_dom=$ip_dom;
          $this->apart=$apart;
          $this->acc=$this->db_get_dom_acc_by_ip($ip_dom);
        }

        public function list()
        {
            $url="http://$this->acc@$this->ip_dom/cgi-bin/rfid_cgi?action=list&Apartment=$this->apart";

            $rcurl=$this->get_curl($url);
          if(strpos($rcurl,'Request failed') === false)
          {
            $res=explode("\n",$rcurl);
// print_r($res);

            $i=0;
            foreach ($res as $val)
            {
               if (strpos($val,'KeyValue') !== false)
               {
                 $res[$i]=preg_replace('/KeyValue\d+=/','',$val); $i++;
               }
               else { array_splice($res, $i, 1); }
            }

            array_unshift($res,"RFYD keys");
            $out_json= json_encode(array("data"=>$res,"status"=>200));
          }
          else
          {
            $out_json= json_encode(array("status"=>400,"error_message"=> $rcurl));
          }
            echo "$out_json";

        }

        public function add($in_key)
        {
            $url="http://$this->acc@$this->ip_dom/cgi-bin/rfid_cgi?action=add&Key=$in_key&Apartment=$this->apart";

            $rcurl=$this->get_curl($url);
            if ($rcurl== "OK") { $out_json= json_encode(array("data"=> "OK","status"=>200)); }
            else { $out_json= json_encode(array("status"=>400,"error_message"=> $rcurl)); }
            echo "$out_json";

        }

        public function del($in_key)
        {
            $url="http://$this->acc@$this->ip_dom/cgi-bin/rfid_cgi?action=delete&Key=$in_key&Apartment=$this->apart";

            $rcurl=$this->get_curl($url);
            if ($rcurl== "OK") { $out_json= json_encode(array("data"=> "OK","status"=>200)); }
            else { $out_json= json_encode(array("status"=>400,"error_message"=> $rcurl)); }
echo "$out_json";

//      echo "$str_curl\n";
// file_put_contents($filename, "$str_json \n", FILE_APPEND);


        }

        private function db_get_dom_acc_by_ip($ip)
        {
             $conn_string= "host=localhost port=5432 dbname='dom' user='postgres' password='postgres'";
             $dbconn= pg_connect($conn_string);

             $query="SELECT dom_user || ':' || convert_from(decode(dom_pass,'base64'),'UTF-8') FROM public.domophons where ip_addr='$ip';";
             $qr= pg_query($dbconn, $query);
             $rs= pg_fetch_array($qr,NULL,PGSQL_NUM);
             pg_close($dbconn);
           return $rs[0];
        }

        private function get_curl($uu)       
        {
            $ch= curl_init($uu);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $str_curl= curl_exec($ch);
            curl_close($ch);

            $str_curl=trim($str_curl,"\n\r");
          return $str_curl;
        }

}


