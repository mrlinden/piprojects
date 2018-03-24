<?php
header("Content-Type: application/json; charset=UTF-8");
$scene = json_decode($_POST["scene"], false);

echo json_encode("Got Scene ".$scene);
?>
