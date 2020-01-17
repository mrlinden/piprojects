<?php
header("Content-Type: application/json; charset=UTF-8");

$msg = $_POST["scene"];
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
$len = strlen($msg);
socket_sendto($sock, $msg, $len, 0, '127.0.0.1', 5000);
socket_close($sock);

echo json_encode("Ok ".$msg);

?>
