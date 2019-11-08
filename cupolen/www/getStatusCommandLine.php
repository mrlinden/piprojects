<?php
#error_reporting(E_ERROR | E_PARSE);


function getAutomationStatus($index)
{
    list($scriptPath) = get_included_files();
    $fileName = dirname($scriptPath) . "/automation." . $index;
    $fp = fopen($fileName, 'r') or die('0');
    $content = fgets($fp);
    fclose($fp);
    if ($content == '1') return '1';
    return '0';
}

echo "A0=" . getAutomationStatus(0) .
     ":A1=" . getAutomationStatus(1) .
     ":A2=" . getAutomationStatus(2);
?>
