<?php
error_reporting(E_ERROR | E_PARSE);

$cmd = $argv[1];
#$address = '127.0.0.1';
$address = '10.254.1.1';
$port = 50001;
$srcPort = 51234;
$name = '';
// Get source address
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_connect($sock, "8.8.8.8", 53);
socket_getsockname($sock, $name); // $name passed by reference
$srcAddress = $name;


$errorState = 0;
$result = 0;
$address = '10.254.1.1';
$port = 50001;

$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
$len = strlen($cmd);
socket_sendto($sock, $cmd, $len, 0, $address, $port);
socket_close($sock);

echo "Ok " . $cmd;
?>
