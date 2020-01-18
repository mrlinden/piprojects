<?php
header("Content-Type: application/json; charset=UTF-8");
$cmd = $_POST["cmd"];
$result = `php ../setSceneCommandLine.php "$cmd"`;
echo json_encode($result);
?>
