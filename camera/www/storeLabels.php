<?php
$ini = parse_ini_file('labels.ini');

echo "let presetLabel = [ [\"", $ini['L11'], "\",\"", $ini['L12'], "\",\"", $ini['L13'], "\",\"", $ini['L14'], "\",\"", $ini['L15'], "\",\"", $ini['L16'], "\",\"", $ini['L17'], "\"]";

?>