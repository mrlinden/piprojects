<?php

// To avoid exposing the config file on web, that file is in parent folder.
// Since we use a symbolic link to deploy the application on the webserver,
// the parent folder will not contain that config file. So we point that out
// with a full path in config-pointer.ini.
// In development environment on Windows that path is not valid but the
// config file in parent folder is accessibe, so we set the "default" path
// to the config file in parent folder.
$configFilePath = "../config.ini";

$configPointerFilePath = "config-pointer.ini";
if (file_exists ($configPointerFilePath)) {
	$pointer = parse_ini_file($configPointerFilePath);
	$pointerVal = $pointer['config'];
	if (file_exists ($pointerVal)) {
		$configFilePath = $pointerVal;
	}
}

$config = null;
if (file_exists ($configFilePath)) {
	$config = parse_ini_file($configFilePath);
} else {
	print "\n\nConfiguration is missing\n";
	exit();
}

$GLOBALS['app_root_path'] = $config['app_root_path'];

// Handle class loading
spl_autoload_register(function ($className) {
    $class_name = substr($className, strrpos($className, '\\') + 1);
    $try = $class_name . '.php';
    if (file_exists($try)) { include_once($try); }
    $try = $GLOBALS['app_root_path'] . 'db/' . $class_name . '.php';
    if (file_exists($try)) { include_once($try); }
    $try = $GLOBALS['app_root_path'] . 'plates-3.1.1/src/' . $class_name . '.php';
    if (file_exists($try)) { include_once($try); }
    $try = $GLOBALS['app_root_path'] . 'plates-3.1.1/src/Template/' . $class_name . '.php';
    if (file_exists($try)) { include_once($try); }
});


// Get configuration from file
$db_host       =  $config['db_host'];
$db_name       =  $config['db_name'];
$db_user       =  $config['db_user'];
$db_password   =  $config['db_password'];


// Open database
try {
	$dbUrl = 'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8';
	$db = new PDO($dbUrl, $db_user, $db_password);
} catch (Exception $e) {
	echo '\n\nFailed to connect to database (',  $e->getMessage(), ")\n";
	exit();
}


// Get data from model
$visits = new Cupolen\Visits($db);
$list = $visits->getVisitsPerDay();
$nrYears = 10;
if (sizeof($ist) > 0) {
	$nrYears = 1 + end($list)['y'] - reset($list)['y'];
}

// Create new Plates instance
$templates = new League\Plates\Engine($app_root_path . 'templates/');

// Render a template
echo $templates->render('year', ['list' => $list, 'nrY' => $nrYears]);


/*
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
