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
	
	print "date: " . $date . " A: " . $a . " B: " . $b . " C: " . $c . " D: " . $d . " \n" ;
	
	$sqlWrite = "INSERT INTO `visits`.`sensordata` (`date`, `id`, `count`) VALUES";
	$addedItem = 0;
	
	if ($a != 0) {
		$sqlWrite .= " (FROM_UNIXTIME(".$date."), 1, ".$a.")";
		$addedItem += 1;
		print "Write A" . $a;
	}

	if ($b != 0) {
		if ($addedItem > 0) $sqlWrite .= ",\n";
		$sqlWrite .= " (FROM_UNIXTIME(".$date."), 2, ".$b.")";
		$addedItem += 1;
		
		print "Write B" . $b;
	}

	if ($c != 0) {
		if ($addedItem > 0) $sqlWrite .= ",\n";
		$sqlWrite .= " (FROM_UNIXTIME(".$date."), 3, ".$c.")";
		$addedItem += 1;
		
		print "Write C" . $c;
	}

	if ($d != 0) {
		if ($addedItem > 0) $sqlWrite .= ",\n";
		$sqlWrite .= " (FROM_UNIXTIME(".$date."), 4, ".$d.")";
		$addedItem += 1;
		
		print "Write D" . $d;
	}
	
	print "SQL: " . $sqlWrite;
	
	#if ($db->query($sqlWrite) === TRUE) {
	#	echo "New record created successfully";
	#} else {
#		echo "Error: " . $sql . "<br>" . $conn->error;
#	}
	
}

?>
