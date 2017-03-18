#!/usr/bin/php
<?php
include 'Visits.php';

$db = new PDO('mysql:host=localhost;dbname=visits;charset=utf8', 'root', 'linden1mysql');

// Get the old content
$sqlQuery = "SELECT * from `minutetable`";
$old = $db->query($sqlQuery);

foreach ($old as $row) {
	
	
		print "date: " . $row['intervalStop'] . " A: " . $row['doorA'] . " B: " . $row['doorB'] . " C: " . $row['doorC'] . " D: " . $row['doorD'] . " visits \n" ;
	}

?>
