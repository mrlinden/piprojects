<?php

spl_autoload_register(function ($className) {
    $class_name = substr($className, strrpos($className, '\\') + 1);
    $try = $class_name . '.php';
    if (file_exists($try)) { include_once($try); }
    $try = 'db/' . $class_name . '.php';
    if (file_exists($try)) { include_once($try); }
    $try = 'plates-3.1.1/src/' . $class_name . '.php';
    if (file_exists($try)) { include_once($try); }
    $try = 'plates-3.1.1/src/Template/' . $class_name . '.php';
    if (file_exists($try)) { include_once($try); }
});

// Get visit data from database
$db = new PDO('mysql:host=localhost;dbname=visits;charset=utf8', 'root', 'linden1mysql');
$visits = new Cupolen\DataModel\Visits($db);

// Create new Plates instance
$templates = new League\Plates\Engine('templates/');

// Render a template
echo $templates->render('year', ['name' => 'Jonathan']);

/*
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
*/

?>
