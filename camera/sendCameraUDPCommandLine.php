<?php
error_reporting(E_ERROR | E_PARSE);

$camNr = $argv[1];
$cmd = hex2bin($argv[2]);

$addressLSB = 12 + $camNr;
$address = '192.168.14.' . $addressLSB;
$port = 5002;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($sock, $address, $port);
socket_write($sock, $cmd);
socket_close($sock);

echo "Ok ";
?>
