<?php
#error_reporting(E_ERROR | E_PARSE);

$cmd = $argv[1];
list($index, $status) = explode(":", $cmd);

if ($index == "0" || $index == "1" || $index == "2") {
    list($scriptPath) = get_included_files();
    $fileName = dirname($scriptPath) . "/status/automation." . $index;
    $content = ($status == "0") ? '0' : '1';
    $fp = fopen($fileName, 'w');
    fwrite($fp, $content);
    fclose($fp);
    echo "Ok " . $cmd . $fileName;
} else {
    echo "Fail";
}

?>
