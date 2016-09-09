#!/usr/bin/php
<?php

$db = new PDO('mysql:host=localhost;dbname=visits;charset=utf8', 'pi', 'linden1mysql');

include 'Visits.php';

// Create an instance
$visits = new Visits($db);

// Get the list of Foos
$dayList = $visits->getVisitsPerDay();

foreach ($dayList as $row):
  print($row['date'] + " : " + $row['visits']);
endforeach;

?>
