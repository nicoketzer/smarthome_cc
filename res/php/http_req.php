<?php 
function call_url($url){
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_HTTPHEADER, array ('content-type: text/html'));
    curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
    $response_body = curl_exec($process);
    $http_code = curl_getinfo($process, CURLINFO_HTTP_CODE);
    if($http_code >= 300) {
      die("Unexpected Response Code: ${http_code}: ${response_body}");
    }
    curl_close($process);
    return $response_body;
}
?>