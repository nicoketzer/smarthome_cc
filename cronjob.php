<?php
include("./res/all.php"); 
//Lokale Cronjob-Datei auf C&C-Server
//Diese Cronjob-Datei wird verwendet für Sachen die mit Verzögerungen ab 
//einer Minute klar kommen (z.B. Update Geräteliste)
//Eine Seperate Cronjob-PHP Datei (cronjob.imp.php) wird alle 500ms per 
//Linux Service ausgeführt

//Diese Cronjob.php nimmt Kommandos vom Webserver entgegen und führt diese aus
//ein Identifier stellt sicher das die Komandos auch vom Autorisierten 
//Webinterface kommen
if(isset($_GET["ident"]) && isset($_GET["comm"])){
    //Ident-String und Kommando gesetzt
    $ident = $_GET['ident'];
    $comm = $_GET['comm'];
    global $cc_cronjob_ident;
    $ident_safed = $cc_cronjob_ident;
    if($ident == $ident_safed){
        //Webserver hat sich identifiziert
        //Jetzt muss online nachgeschaut werden um welchen Kommand das es sich handelt
        //Vordefinierte Commandos herausfiltern
        if($comm == "get_fb_dev"){
            //Alle Geräte bekommen (Verarbeitung findet auch sofort statt)
            $obj = new get_Wifi_state();
            $obj->def_all_hosts(null);
            $obj->get_all(true);
            $all_hosts = $obj->get_for_all_hosts();
            $obj = null; 
            //Verarbeitung
            $proc = new proc_data($all_hosts);
            $proc->proc_now();
            $proc = null;
            //Fertig
            echo "ok";       
        }else{
            $sql = "SELECT * FROM `token_action` WHERE `token`='" . bin2hex($comm) . "'";
            $mysqli = new_mysqli();
            $res = sql_result_to_array(start_sql($mysqli,$sql));
            close_mysqli($mysqli);
            //Hier nun das Kommando von hex nach bin umwandeln
            $comm_exec = hex2bin($res[0]["action"]);
            //Ausführen
            $comm_type = hex2bin($res[0]["action_type"]);
            if($comm_type == "php_skript"){
                exec($comm_exec);
            }else if($comm_type == "http_req"){
                //Sollte es sich um den Typ http_req handeln so ist der 
                //Befehl in $comm_exec der URL der aufgerufen werden soll
                $url = $comm_exec;
                //Anfrage Starten
                $erg = call_url($url);
                //Rückgabe zurückgeben
                echo $erg;    
            }else{
                echo "No Command Found";
            }
        }
    }else{
        //Nicht vertrauenswürdig
        echo "Not Allowed";
        exit;
    }    
}else{
    echo "Not Found";
    exit;
}
?>