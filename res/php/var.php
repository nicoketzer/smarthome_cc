<?php
//Hier werden zuk�nftig Globale Variablen (bzw. Public-Variablen) 
//eingef�gt werden

//Variablen die Ge�ndert werden d�rfen
#Zugansdaten Mysql-Server Remote
$mysqli_server = "";
$mysqli_bn = "";
$mysqli_pw = "";
$mysqli_db = "";
#Zugangsdaten Lokaler Mysql-Server
$mysqli_offline_server = "";
$mysqli_offline_bn = "";
$mysqli_offline_pw = "";
$mysqli_offline_db = "";

#Zugangsdaten Mysql-Server lokal
$local_mysql_bn = "";
$local_mysql_pw = "";
$local_mysql_db = "";

//Variablen die nicht ge�ndert werden d�rfen da ggf. die Funktion sonst nicht mehr 
//gegeben ist
$cc_bind_token = "";
$cc_cronjob_ident = "";
$cc_work_ident = "";
$cc_ip_update_token = "";
$cc_port_extern = "";
$cc_server_addr = "";
$cc_server_hostname = "";
$cc_server_name = "";
?>