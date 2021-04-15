<?php
//Hier werden zukünftig Globale Variablen (bzw. Public-Variablen) 
//eingefügt werden

//Variablen die Geändert werden dürfen
#Zugansdaten Mysql-Server Remote
$mysqli_server = "__MYSQLI_SERVER__";
$mysqli_bn = "__MYSQLI_BN__";
$mysqli_pw = "__MYSQLI_PW__";
$mysqli_db = "__MYSQLI_DB__";
#Zugangsdaten Lokaler Mysql-Server
$mysqli_offline_server = "__MYSQLI_OFFLINE_SERVER__";
$mysqli_offline_bn = "__MYSQLI_OFFLINE_BN__";
$mysqli_offline_pw = "__MYSQLI_OFFLINE_PW__";
$mysqli_offline_db = "__MYSQLI_OFFLINE_DB__";

//Variablen die nicht geändert werden dürfen da ggf. die Funktion sonst nicht mehr 
//gegeben ist
$cc_bind_token = "__CC_BIND_TOKEN__";
$cc_cronjob_ident = "__CC_CRONJOB_IDENT__";
$cc_work_ident = "__CC_WORK_IDENT__";
$cc_ip_update_token = "__CC_IP_UPDATE_TOKEN__";
$cc_port_extern = "__CC_PORT_EXTERN__";
$cc_server_addr = "__CC_SERVER_ADDR__";
$cc_server_hostname = "__CC_SERVER_HOSTNAME__";
$cc_server_name = "__CC_SERVER_NAME__";
?>