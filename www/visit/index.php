<?php

$db = new PDO('mysql:host=localhost;dbname=visits;charset=utf8', 'root', 'linden1mysql');

include 'db/Visits.php';
$visits = new Visits($db);

include 'views/header.php';

if (isset($_GET['year'])) {
	$list = $visits->getVisitsPerDay($_GET['year']);
	include 'views/year.php';
} elseif (isset($_GET['date'])) {
	$list = $visits->getVisitsPerDay($_GET['date']);
	include 'views/day.php';
} else {
	$list = $visits->getVisitsPerDay();
	include 'views/year.php';
}
include 'views/footer.php';

?>