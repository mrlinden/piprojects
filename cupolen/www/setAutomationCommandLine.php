<?php
#error_reporting(E_ERROR | E_PARSE);

$cmd = $argv[1];
list($auto, $status) = explode(":", $cmd);

if ($auto == "0" || $auto == "1" || $auto == "2") {
    list($scriptPath) = get_included_files();
    $fileName = dirname($scriptPath) . "/automation." . $auto;
    $content = ($status == "1") ? '1' : '0';
    $fp = fopen($fileName, 'w');
    fwrite($fp, $content);
    fclose($fp);
    echo "Ok " . $cmd;
} else {
    echo "Fail";
}

?>
