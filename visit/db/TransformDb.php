#!/usr/bin/php
<?php
include 'Visits.php';

$db = new PDO('mysql:host=localhost;dbname=visits;charset=utf8', 'root', 'linden1mysql');

// Get the old content
$sqlQuery = "SELECT UNIX_TIMESTAMP(intervalStop) AS date, doorA, doorB, doorC, doorD from `minutetable`";
$old = $db->query($sqlQuery);

foreach ($old as $row) {

	$date = floor($row['date']/600)*600;
	$a = $row['doorA'];
	$b = $row['doorB'];
	$c = $row['doorC'];
	$d = $row['doorD'];
	
	print "date: " . $row['date'] . " A: " . $row['doorA'] . " B: " . $row['doorB'] . " C: " . $row['doorC'] . " D: " . $row['doorD'] . " visits \n" ;
	
	print "floor " . $date . "\n";
	
	$sqlWrite = "INSERT INTO `visits`.`sensordata` (`date`, `id`, `count`) VALUES (%s,%s,%s)";
	
	if ($a != 0) {
		print "Write A" . $a;
	}

	if ($b != 0) {
		print "Write B" . $b;
	}

	if ($c != 0) {
		print "Write C" . $c;
	}

	if ($d != 0) {
		print "Write D" . $d;
	}
	
	
}

?>
