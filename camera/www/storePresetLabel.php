<?php
header("Content-Type: application/json; charset=UTF-8");
$labelId = $_POST["labelId"];
$labelText = $_POST["labelText"];
$result = `php storePresetLabelCommandLine.php "$labelId" "$labelText"`;
echo json_encode($result);
?>
