<?php 
//Einbinden aller Funktionen
include("/res/all.php");
//Lokale Cronjob-Datei auf C&C-Server
//Diese zum Cronjob zugehörige Datei wird verwendet für Sachen die eine geringe Verzögerungen benötigen
//(<=500ms) verwendet. Diese wird per Terminal-Service ausgeführt
//Diese Datei ist speziel dafür da die Lokale DB und die Online-DB zu Syncronisieren damit diese
//Verzögerung bei der cronjob.imp.php gespart werden kann
//Wird 1x pro Minute ausgeführt das auch Performance-Technisch nicht zu Krass wird

//Da es sowieso nur von CJ ausgeführt wird kann in der .htaccess Datei der Zugriff vom Webserver 
//aus unterbunden werden
//Anbindung an externen Cronjob des Webserver ist evtl sinnvoll jedoch bis jetzt noch nicht 
//umgesetzt

class main{
    private $online_el = array();
    private $offline_el = array();
    private $online_token = array();
    private $offline_token = array();
    private $online_id = array();
    private $offline_id = array();
    private $changed_el = array();
    private $new_el = array();
    private $del_el = array();
    public function __construct(){
        return true;
    }
    public function get_online_db(){
        //Alle Tokens bekommen
        $sql = "";
        //SQL-Verbindung zum Server herstellen
        $mysqli = new_mysqli();
        //Sql-Befehl ausführen
        $res = sql_result_to_array(start_sql($mysqli,$sql));
        //Überprüfen
        if(isset($res[0])){
            //Array hat min. einen Eintrag
            $this->online_el = $res;
        }
        //Schließen der Verbindung
        close_mysqli($mysqli);    
    }
    public function get_offline_db(){
        //Alle Tokens bekommen
        $sql = "";
        //SQL-Verbindung zum Server herstellen
        $mysqli = new_offline_mysqli();
        //Sql-Befehl ausführen
        $res = sql_result_to_array(start_sql($mysqli,$sql));
        //Überprüfen
        if(isset($res[0])){
            //Array hat min. einen Eintrag
            $this->offline_el = $res;
        }
        //Schließen der Verbindung
        close_mysqli($mysqli);    
    }
    public function find_changes(){
        //Es wird davon ausgegangen das die Online db aktueller ist (was ja stimmt) und somit werden 
        //die Elemente der Online DB abgearbeitet
        //Vorbereiten Offline Array
        foreach($this->offline_el as $get_token){
            $token = $get_token[""];
            $id = $get_token[""];
            array_push($this->offline_token,$token);
            array_push($this->offline_id,$id);
        }
        //Vorbereiten Online Array
        foreach($this->online_el as $get_token){
            $token = $get_token[""];
            $id = $get_token[""];
            array_push($this->online_token,$token);
            array_push($this->online_id,$id);
        }
        //Neue Elemente finden
        foreach($this->online_token as $token){
            if(!in_array($token,$this->offline_token)){
                //Element exisitert noch nicht also hinzufügen
                $pos = array_search($token,$this->online_token);
                //Zugehörige ID finden
                $id = $this->online_id[$pos];
                //Element erzeugen
                $el = array($token,$id);
                //Hinzufügen
                array_push($this->new_el,$el);    
            }
        }
        //Alte Elemente finden
        foreach($this->offline_token as $token){
            if(!in_array($token,$this->online_token)){
                //Element exisitert nicht mehr also entfernen
                array_push($this->del_el,$token);
            }
        }
        //Aktualisierte elemente finden          
        foreach($this->online_token as $test_token){
            if(in_array($test_token,$this->offline_token)){
                //Zugehörige ID´s bekommen
                $pos_online_el = array_search($test_token,$this->online_token);
                $pos_offline_el = array_search($test_token,$this->offline_token);
                $online_id_el = $this->online_id[$pos_online];
                $offline_id_el = $this->offline_id[$pos_offline];
                //ID´s vergleichen (bzw. den ausführinhalt);
                if($online_id_el != $offline_id_el){
                    //Es hat sich was geändert
                    array_push($this->changed_el,array($test_token,$online_id_el));
                }
            }
        }        
    }
    public function work_off(){
        $mysqli = new_offline_mysqli();
        foreach($this->changed_el as $aktu_el){
            $sql = "";
            start_sql($mysqli,$sql);    
        }
        foreach($this->del_el as $delete_el){
            $sql = "";
            start_sql($mysqli,$sql);
        }
        foreach($this->new_el as $create_el){
            $sql = "";
            start_sql($mysqli,$sql);
        }
        close_mysqli($mysqli);    
    }    
}


//Aufrufen der Main-Klasse
$obj = new main();
$obj->get_online_db();
$obj->find_changes();
$obj->work_off();
?>