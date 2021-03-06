<?php
class get_Wifi_state{
    public $client;
    public $fritzbox_ip;
    public $tr64_port;
    public $hosts;
    public $options;
    public $context;
    public $NumberOfHosts;
    public $hosts_state;
    public $get_all;
    public $host_sort;
    public $host_tmp;
    public function __construct(){
        $this->client = null;
        $this->fritzbox_ip = "192.168.178.1";
        $this->tr64_port = "49000";
        $this->hosts = array();
        $this->options = array();
        $this->context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
        $this->NumberOfHosts = null;
        $this->client = null;
        $this->hosts_state = array();
        $this->get_all = false;
        $this->host_sort = array();
        $this->host_tmp = array();   
    }
    
    private function get_fb_data(){
    // SOAP Abfrage:
        $this->client = new SoapClient(null,array('location'	=> "http://" . $this->fritzbox_ip . ":" . $this->tr64_port . "/upnp/control/hosts",
        									'uri'		=> "urn:dslforum-org:service:Hosts:1",
        									'soapaction'=> "urn:dslforum-org:service:Hosts:1#GetSpecificHostEntry",
        									'noroot'	=> False
        ));    
        $this->NumberOfHosts = $this->client->GetHostNumberOfEntries();
        if (!is_soap_fault($this->NumberOfHosts)){
            for($i=0; $i<$this->NumberOfHosts; $i++){
                $Host = $this->client->GetGenericHostEntry(new SoapParam($i,'NewIndex'));
                $hostname = $Host['NewHostName'];
                $host_state = $Host['NewActive'];
                $push_back = array('hostname'=>$hostname,'state'=>$host_state);
                if(!in_array($push_back,$this->hosts_state)){
                    array_push($this->hosts_state,$push_back);
                }
            }
        }
    }
    public function def_all_hosts($array_over){
        $this->hosts = $array_over;
    }
    public function get_all($state){
        $this->get_all = $state;    
    }
    public function exist_all_hosts(){
        //Es wird $hosts mit $hosts_state verglichen
        //Es wird get_fb_data() ausgef�hrt
        $this->get_fb_data();
        $all_exists = true;
        if(isset($this->hosts[0])){
            //Vergleich starten
            foreach($this->hosts_state as $test_host){
                $hostname = $test_host['hostname'];
                if(!in_array($hostname,$this->host_tmp)){
                    array_push($this->host_tmp,$hostname);
                }        
            }
            foreach($this->hosts as $test){
                if(!in_array($test,$this->host_tmp)){
                    $all_exists = false;
                }
            }
            //R�ckgabe
            return $all_exists;
        }else{
            //Es wurden noch keine Hosts definiert sprich es existieren 
            //alle definierten Hosts
            return $all_exists;
        }    
    }
    public function get_for_all_hosts(){
        //Abfrage ob �berhaupt ein host vorher festgelegt wurde
        if(isset($this->hosts[0])){
            //Es wurde mindestens ein Host vorher festgelegt
            //Alle Ger�te abfragen und Status speichern
            $this->get_fb_data();
            //Jetzt sind alle Ger�te mit Status in $hosts_state gespeichert
            foreach($this->hosts_state as $test_host){
                $host_name = $test_host["hostname"];
                if(in_array($host_name,$this->hosts)){
                    array_push($this->host_sort,$test_host);    
                }    
            }
            return $this->host_sort;    
        }else{
            //Es wurde kein Host vorher festgelegt
            #Abfrage ob alle Clients zur�ckgeschickt werden sollen oder ein 
            #fehler String
            if($this->get_all){
                $this->get_fb_data();
                //Alle Hosts sollen zur�ckgeschickt werden
                return $this->hosts_state;
            }else{
                return "No Hosts were given";
            }
        }
    }
}
class proc_data{
    private $hosts;
    private $error;
    private $dublicate;
    public function __construct($given_hosts){
        $this->hosts = $given_hosts;
        $this->error = false;
        $this->dublicate = array();        
    }
    public function proc_now(){
        if(isset($this->hosts[0])){
            $this->test_for_double_hostnames();
            foreach($this->hosts as $fill_in){
                $this->into_normal_db($fill_in);   
            }    
        }
    }
    private function into_error_db($hostname){
        //Erst testen ob schon Datensatz vorhanden ist
        $sql = "SELECT * FROM `router-dev-error` WHERE `hostname`='" . bin2hex($hostname) . "'";
        $mysqli = new_mysqli();
        $res = sql_result_to_array(start_sql($mysqli,$sql));
        if(!isset($res[0])){
            //Datensatz exisiteirt noch nicht
            global $cc_server_name;
            $cc_server = $cc_server_name;
            $sql = "INSERT INTO `router-dev-error`(`cc_server`, `hostname`, `type`) VALUES ('" . $cc_server . "','" . bin2hex($hostname) . "','1')";
            start_sql($mysqli,$sql);
        }
        close_mysqli($mysqli);
        return true;    
    }
    private function test_for_double_hostnames(){
        $tmp_array = array();
        foreach($this->hosts as $test){
            if(!in_array($test["hostname"],$tmp_array)){
                array_push($tmp_array,$test["hostname"]);        
            }else{
                //Dublicat erkannt
                $this->into_error_db($test["hostname"]);
            }
        }
        $tmp_array = null;    
    }
    private function into_normal_db($set){
        global $cc_server_name;
        //Erst testen ob schon Datensatz vorhanden ist
        $sql = "SELECT * FROM `router-dev` WHERE `hostname`='" . bin2hex($set["hostname"]) . "' AND `cc_server`='" . bin2hex($cc_server_name) . "'";
        $mysqli = new_mysqli();
        $res = sql_result_to_array(start_sql($mysqli,$sql));
        if(!isset($res[0])){
            //Datensatz exisiteirt noch nicht
            global $cc_server_name;
            $cc_server = $cc_server_name;
            $hostname = $set["hostname"];
            $state = $set["state"];
            $sql = "INSERT INTO `router-dev`(`cc_server`, `hostname`, `state`, `last_update`) VALUES ('" . bin2hex($cc_server) . "','" . bin2hex($hostname) . "','" . $state . "','" . time() . "')";
            start_sql($mysqli,$sql);
        }else{
            global $cc_server_name;
            $cc_server = $cc_server_name;
            $hostname = $set["hostname"];
            $state = $set["state"];
            $sql = "UPDATE `router-dev` SET `state`='" . $state . "', `last_update`='" . time() . "' WHERE `hostname`='" . bin2hex($hostname) . "' AND `cc_server`='" . bin2hex($cc_server) . "'";
            start_sql($mysqli,$sql);
        }
        close_mysqli($mysqli);
        return true;    
    }
}
?>