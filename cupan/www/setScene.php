<?php
header("Content-Type: application/json; charset=UTF-8");
$scene = $_POST["scene"];

echo json_encode("Got Scene ".$scene."....");
?>
