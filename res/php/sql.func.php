<?php 
//Mysql Anfrage starten
if(!function_exists("start_sql")){
    function start_sql($mysqli,$sql){
        if($mysqli->prepare($sql)){
            $statement = $mysqli->prepare($sql);
            $statement->execute();
        }else{
            return mysqli_error($mysqli);
        }
        $result = $statement->get_result();
        return $result;
    }
}
//Sql - Resultat als Array zur&uuml;ckgeben
if(!function_exists("sql_result_to_array")){
    function sql_result_to_array($result){
        if($result){
            $array = array();
            while($row = $result->fetch_assoc()) {
                array_push($array,$row);
            }
            return $array;
        }else{
            return array(false);
        }
    }
}
//Neue Verbindung aufbauen mit Daten aus var.php
if(!function_exists("new_mysqli")){
function new_mysqli(){
    global $mysqli_bn;
    global $mysqli_pw;
    global $mysqli_db;
    global $mysqli_server;
    $mysqli = new mysqli($mysqli_server,$mysqli_bn,$mysqli_pw,$mysqli_db);
    //echo mysqli_get_host_info($mysqli);
    if ($mysqli->connect_errno) {
        die("Verbindung fehlgeschlagen: " . $mysqli->connect_error);
    }else{
        return $mysqli;
    }
}
}
if(!function_exists("new_mysqli")){
function new_offline_mysqli(){
    global $mysqli_offline_bn;
    global $mysqli_offline_pw;
    global $mysqli_offline_db;
    global $mysqli_offline_server;
    $mysqli = new mysqli($mysqli_offline_server,$mysqli_offline_bn,$mysqli_offline_pw,$mysqli_offline_db);
    //echo mysqli_get_host_info($mysqli);
    if ($mysqli->connect_errno) {
        die("Verbindung fehlgeschlagen: " . $mysqli->connect_error);
    }else{
        return $mysqli;
    }
}
}
//Verbindung schließen
if(!function_exists("close_mysqli")){
function close_mysqli($mysqli){
    return $mysqli->close();
}
}
?>