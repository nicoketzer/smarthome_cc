<?php 
if(!function_exists("read_file")){
function read_file($file){
    if(file_exists($file)){
        $fs = filesize($file);
        if($fs >= 1){
            //Dateiinhalt auslesen
            $handle = fopen($file,"r");
            $back = fread($handle,$fs);
            fclose($handle);
            //Ausgelesene Datei zur�ckgeben
            return $back;
        }else{
            //Dateigr��e = 0 sprich leer
            //Leeren String zur�ckgeben
            return "";
        }
    }else{
        //Datei existiert nicht also leeren String zur�ckgeben
        return "";
    }
}
}
if(!function_exists("write_file")){
function write_file($dateiname,$dateiinhalt,$modus){
    $handle = fopen($dateiname,$modus);
    fwrite($handle,$dateiinhalt);
    fclose($handle);
    return true;
}
}
?>