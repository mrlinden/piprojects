<?php
error_reporting(E_ERROR | E_PARSE);

$camNr = $argv[1];
$cmd = hex2bin($argv[2]);

$addressLSB = 12 + $camNr;
$address = '192.168.14.' . addressLSB;
$port = 5002;

$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
$len = strlen($cmd);
socket_sendto($sock, $cmd, $len, 0, $address, $port);
socket_close($sock);

echo "Ok " . $cmd;
?>
