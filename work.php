<?php
include("./res/all.php");
//Lokale-Work Datei
//Diese wird verwendet wenn eine Benutzer anfragen Stellt (damit cronjob.php oder cronjob.imp.php 
//nicht überlastet wird)
if(isset($_GET["ident"]) && isset($_GET["comm"])){
    //Ident-String und Kommando gesetzt
    $ident = $_GET['ident'];
    $comm = $_GET['comm'];
    global $cc_work_ident;
    $ident_safed = $cc_work_ident;
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