<?php
header("Content-Type: application/json; charset=UTF-8");
$camNr = $_POST["camNr"];
$cmd = $_POST["cmd"];
$result = `php ../sendCameraCommandLine.php "$camNr" "$cmd"`;
echo json_encode($result);
?>
