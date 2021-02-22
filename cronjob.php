<?php
include("res/all.php"); 
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
    $ident_safed = read_file("res/data/cc_cronjob_ident.txt");
    if($ident == $ident_safed){
        //Webserver hat sich identifiziert
        //Jetzt muss online nachgeschaut werden um welchen Kommand das es sich handelt
        $sql = "[SQL_ZUR_COMMANDO_ABFRAGE]";
        $mysqli = new_mysqli();
        $res = sql_result_to_array(start_sql($mysqli,$sql));
        close_mysqli($mysqli);
        //Hier nun das Kommando von hex nach bin umwandeln
        $comm_exec = hex2bin($res[0]["COMMANDO_AUS_ZEILE"]);
        //Ausführen
        exec($comm_exec);
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