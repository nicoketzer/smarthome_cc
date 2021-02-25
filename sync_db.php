<?php 
//Lokale Cronjob-Datei auf C&C-Server
//Diese zum Cronjob zugehörige Datei wird verwendet für Sachen die eine geringe Verzögerungen benötigen
//(<=500ms) verwendet. Diese wird per Terminal-Service ausgeführt
//Diese Datei ist speziel dafür da die Lokale DB und die Online-DB zu Syncronisieren damit diese
//Verzögerung bei der cronjob.imp.php gespart werden kann
//Wird 1x pro Minute ausgeführt das auch Performance-Technisch nicht zu Krass wird

?>