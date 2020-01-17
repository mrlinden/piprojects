<?php
#error_reporting(E_ERROR | E_PARSE);

$cmd = $argv[1];

if ($cmd == '0') {
    $result =                  `php ./sendHelvarUDPCommandLine.php ">V:1,C:11,G:130,K:1,B:1,S:1,F:50#"`;
    $result = $result . "\n" . `php ./sendHelvarUDPCommandLine.php ">V:1,C:11,G:131,K:1,B:1,S:1,F:50#"`;
    $result = $result . "\n" . `php ./sendHelvarUDPCommandLine.php ">V:1,C:11,G:132,K:1,B:1,S:1,F:50#"`;
    $result = $result . "\n" . `php ./sendHelvarUDPCommandLine.php ">V:1,C:11,G:133,K:1,B:1,S:1,F:50#"`;
    $result = $result . "\n" . `php ./sendHelvarUDPCommandLine.php ">V:1,C:11,G:133,K:1,B:1,S:1,F:50#"`;
    $result = $result . "\n" . `php ./sendHelvarUDPCommandLine.php ">V:1,C:13,G:129,L:50,F:500#"`;
    echo "Ok " . $cmd;
} else if ($cmd == '1') {
    $result =                  `php ./sendHelvarUDPCommandLine.php ">V:1,C:13,G:129,L:15,F:500#"`;
    echo "Ok " . $cmd;
} else {
    echo "Fail";
}

?>
