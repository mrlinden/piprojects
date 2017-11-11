#!/usr/bin/php
<?php
include 'Visits.php';

print "You need to fill in password!"
$db = new PDO('mysql:host=localhost;dbname=visits;charset=utf8', 'visitDbAdmin', '');

// Get the old content
$sqlQuery = "SELECT UNIX_TIMESTAMP(intervalStop) AS date, doorA, doorB, doorC, doorD from `minutetable`";
$old = $db->query($sqlQuery);

foreach ($old as $row) {

	$date = date("Y-m-d H:i:s", floor($row['date']/600)*600);
	$a = $row['doorA'];
	$b = $row['doorB'];
	$c = $row['doorC'];
	$d = $row['doorD'];
	
	#print "date: " . $date . " A: " . $a . " B: " . $b . " C: " . $c . " D: " . $d . " \n" ;
	
	$sqlWrite = "INSERT INTO `visits`.`sensordata` (`timestamp`, `sensorId`, `count`) VALUES";
	$addedItem = 0;
	
	if ($a != 0) {
		$sqlWrite .= " ('".$date."', 1, ".$a.")";
		$addedItem += 1;
	}

	if ($b != 0) {
		if ($addedItem > 0) $sqlWrite .= ", ";
		$sqlWrite .= " ('".$date."', 2, ".$b.")";
		$addedItem += 1;
	}

	if ($c != 0) {
		if ($addedItem > 0) $sqlWrite .= ", ";
		$sqlWrite .= " ('".$date."', 3, ".$c.")";
		$addedItem += 1;
	}

	if ($d != 0) {
		if ($addedItem > 0) $sqlWrite .= ", ";
		$sqlWrite .= " ('".$date."', 4, ".$d.")";
		$addedItem += 1;
	}
	
	print "SQL: " . $sqlWrite . "\n";
	$db->query($sqlWrite);
}

?>
