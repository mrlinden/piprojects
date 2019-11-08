<?php
header("Content-Type: application/json; charset=UTF-8");

$cmd = $_POST["cmd"];
list($auto, $status) = explode(":", $cmd);

if ($auto == "0" || $auto == "1" || $auto == "2") {

    $fileName = "/home/pi/piprojects/cupolen/www/automation" + $auto;
    $content = ($status == "1") ? 'ON' : 'OFF';
    $fp = fopen($fileName, 'w');
    fwrite($fp, $content);
    fclose($fp);
}

echo json_encode("Ok ".$cmd);

?>
