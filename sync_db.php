<?php 
//Lokale Cronjob-Datei auf C&C-Server
//Diese zum Cronjob zugeh�rige Datei wird verwendet f�r Sachen die eine geringe Verz�gerungen ben�tigen
//(<=500ms) verwendet. Diese wird per Terminal-Service ausgef�hrt
//Diese Datei ist speziel daf�r da die Lokale DB und die Online-DB zu Syncronisieren damit diese
//Verz�gerung bei der cronjob.imp.php gespart werden kann
//Wird 1x pro Minute ausgef�hrt das auch Performance-Technisch nicht zu Krass wird

?>