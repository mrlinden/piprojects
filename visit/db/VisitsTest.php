#!/usr/bin/php
<?php
include 'Visits.php';

$db = new PDO('mysql:host=localhost;dbname=visits;charset=utf8', 'root', 'linden1mysql');

// Create an instance
$visits = new Cupolen\Visits($db);

// Get the list of visits
$dayList = $visits->getVisitsPerDay();

foreach ($dayList as $row) {
  	print "Date : " . $row['date'] . " had " . $row['count'] . " visits \n" ;

	// Get the list of visits for each day
	$minuteList = $visits->getVisitsPerMinute($row['date']);
	
	foreach ($minuteList as $row2) {
		print " - Interval : " . $row2['h'] . "-" . $row2['m'] . "-" . $row2['s'] . " had " . $row2['count'] . " visits \n" ;
	}
}

print "Number of years; " . $visits->getNrOfYearsFromFirstToLastVisit(). " \n";

$minuteList = $visits2->getVisitsPerMinute("2016-09-25");
$arraylist = $minuteList->fetchAll();

foreach ($minuteList as $row2) {
	print "getVisitsPerMinute - Interval : " . $row2['h'] . "-" . $row2['m'] . "-" . $row2['s'] . " had " . $row2['count'] . " visits \n" ;
}
?>
