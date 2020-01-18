<?php
error_reporting(E_ERROR | E_PARSE);

function markStatusRequested()
{
    list($scriptPath) = get_included_files();
    $fileName = dirname($scriptPath) . "/status/statusRequested.txt";
    if (!touch($fileName)) {
        return 'FAILED_TO_UPDATE_' . $fileName;
    }
    return '';
}

function getStatusFromFile($fileName)
{
    list($scriptPath) = get_included_files();
    $fileName = dirname($scriptPath) . "/status/" . $fileName;

    if (!file_exists($fileName)) {
        return '?';
    }

    $fp = fopen($fileName, 'r');
    $content = fgets($fp);
    fclose($fp);
    // Remove any space and new lines
    $pattern = '/\s*/m';
    $replace = '';
    $content = preg_replace( $pattern, $replace, $content );
    return $content;
}

function getAutomationStatus($index)
{
    $content = getStatusFromFile("automation.". $index);
    if ($content == '0') return '0';
    return '1'; // Default to automation On
}

function getDimmerLampLevel($group)
{
    $content = getStatusFromFile("dimmerLamp.". $group);
    if ($content == '?') return '0';
    return $content;
}

function getOnOffLampScene($group)
{
    $content = getStatusFromFile("onOffLamp.". $group);
    if ($content == '?') return '0';
    return $content;
}

echo "A0=" . getAutomationStatus(0) .
     ":A1=" . getAutomationStatus(1) .
     ":A2=" . getAutomationStatus(2) .
     ":L130=" . getOnOffLampScene(130) .
     ":L131=" . getOnOffLampScene(131) .
     ":L132=" . getOnOffLampScene(132) .
     ":L133=" . getOnOffLampScene(133) .
     ":L900=" . getDimmerLampLevel(900) .
     ":L129=" . getDimmerLampLevel(129) .
     ":L500=" . getOnOffLampScene(500) .
     ":L501=" . getOnOffLampScene(501) .
     ":" . markStatusRequested();
?>
