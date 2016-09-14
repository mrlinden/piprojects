#!/usr/bin/php

<?php
include 'Visits.php';

$db = new PDO('mysql:host=localhost;dbname=visits;charset=utf8', 'root', 'linden1mysql');

// Create an instance
$visits = new Cupolen\Visits($db);

// Get the list of visits
$dayList = $visits->getVisitsPerDay();

foreach ($dayList as $row):
  print "Date : " . $row['date'] . " had " . $row['visits'] . " visits \n" ;
endforeach;

// Get the list of visits for each day
foreach ($dayList as $row):
	$minuteList = $visits->getVisitsPerMinute($row['date']);
	
	foreach ($minuteList as $row2):
	print "Time : " . $row2['intervalStart'] . " had " . $row['doorA'] . " visits \n" ;
	endforeach;
endforeach;


?>
