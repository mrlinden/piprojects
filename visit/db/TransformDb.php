#!/usr/bin/php
<?php
include 'Visits.php';

$db = new PDO('mysql:host=localhost;dbname=visits;charset=utf8', 'root', 'linden1mysql');

// Get the old content
$sqlQuery = "SELECT UNIX_TIMESTAMP(intervalStop) AS date, doorA, doorB, doorC, doorD from `minutetable`";
$old = $db->query($sqlQuery);

foreach ($old as $row) {
		print "date: " . $row['date'] . " A: " . $row['doorA'] . " B: " . $row['doorB'] . " C: " . $row['doorC'] . " D: " . $row['doorD'] . " visits \n" ;

	$date = floor($row['date']/600)*600;
	
	print "floor " . $date . "\n";
	
}

?>
