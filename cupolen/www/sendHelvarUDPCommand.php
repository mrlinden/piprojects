<?php
header("Content-Type: application/json; charset=UTF-8");

$cmd = $_POST["cmd"];
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
$len = strlen($cmd);
socket_sendto($sock, $cmd, $len, 0, '127.0.0.1', 50001);
socket_close($sock);

echo json_encode("Ok ".$cmd);

?>
