<?php
error_reporting(E_ERROR | E_PARSE);

$index = '';
if ($argc > 1) {
    $index = $argv[1];
}

function getAutomationStatus($index)
{
    list($scriptPath) = get_included_files();
    $fileName = dirname($scriptPath) . "/../status/automation." . $index;

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

if ($index == '') {
    // Restore to "Automation On" by removing the status files (On is default when no file exist)
    $result = `rm -rf ../status/automation*`;
} else if (getAutomationStatus($index) == '1') {
    $result =  `php ../setSceneCommandLine.php "$index"`;
}
echo $result;
?>
