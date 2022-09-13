<?php

$width = 0;
$height = 0;

if(isset($_POST['width']) && isset($_POST['height'])) {
    $width = $_POST['width'];
    $height = $_POST['height'];
    $page = $_POST['page'];
    if (empty($page)) $page = "home";
}
 
date_default_timezone_set('America/Sao_Paulo');

//ASSIGN VARIABLES TO USER INFO
$time = date("M j G:i:s Y"); 
$ip = getenv('REMOTE_ADDR');  
 
//COMBINE VARS INTO OUR LOG ENTRY
$msg = "IP: " . $ip . " # TIME: " . $time . " # PAG:".$page . " # ". $width."x".$height;
 
//CALL OUR LOG FUNCTION
writeToLogFile($msg);
 
function writeToLogFile($msg) { 
     if  (!$handle = @fopen("log.txt", "a")) {
          echo "erro";
          exit;
     }
     else {
          if (@fwrite($handle,"$msg\r\n") === FALSE) {
               exit;
          }
   
          @fclose($handle);
     }
}
 
?>