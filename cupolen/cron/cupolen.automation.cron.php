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
    // Restore to automation On by removing the status files (On is default when no file exist)
    $result = `rm -rf ../status/automation*`;
} else if (getAutomationStatus($index) == '1') {
    // If automation in On, send the corresponding automation commands
    if ($index == '0') {
        $result = `php ../sendHelvarUDPCommandLine.php ">V:1,C:11,G:130,K:1,B:1,S:1,F:50#"`;
        $result = $result . "\n" . `php ../sendHelvarUDPCommandLine.php ">V:1,C:11,G:131,K:1,B:1,S:1,F:50#"`;
        $result = $result . "\n" . `php ../sendHelvarUDPCommandLine.php ">V:1,C:11,G:132,K:1,B:1,S:1,F:50#"`;
        $result = $result . "\n" . `php ../sendHelvarUDPCommandLine.php ">V:1,C:11,G:133,K:1,B:1,S:1,F:50#"`;
        $result = $result . "\n" . `php ../sendHelvarUDPCommandLine.php ">V:1,C:11,G:133,K:1,B:1,S:1,F:50#"`;
        $result = $result . "\n" . `php ../sendHelvarUDPCommandLine.php ">V:1,C:13,G:129,L:50,F:500#"`;
    } else if ($index == '1') {
        $result = `php ../sendHelvarUDPCommandLine.php ">V:1,C:13,G:129,L:15,F:500#"`;
    }
}
echo $result;
?>
