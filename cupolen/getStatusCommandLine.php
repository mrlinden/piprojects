<?php
error_reporting(E_ERROR | E_PARSE);

function getAutomationStatus($index)
{
    list($scriptPath) = get_included_files();
    $fileName = dirname($scriptPath) . "/status/automation." . $index;

    if (!file_exists($fileName)) {
        return '1';
    }

    $fp = fopen($fileName, 'r');
    $content = fgets($fp);
    fclose($fp);
    // Remove any space and new lines before checking the content
    $pattern = '/\s*/m';
    $replace = '';
    $content = preg_replace( $pattern, $replace, $content );
    if ($content == '0') return '0';
    return '1';
}

echo "A0=" . getAutomationStatus(0) .
     ":A1=" . getAutomationStatus(1) .
     ":A2=" . getAutomationStatus(2);
?>
