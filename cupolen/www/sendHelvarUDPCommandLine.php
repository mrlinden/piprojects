<?php
#error_reporting(E_ERROR | E_PARSE);

$cmd = $argv[1];
$address = '10.254.1.1';
$port = 50001;

$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
$len = strlen($cmd);
socket_sendto($sock, $cmd, $len, 0, $address, $port);
socket_close($sock);

echo "Ok " . $cmd;
?>
