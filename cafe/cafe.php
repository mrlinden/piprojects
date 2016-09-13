<?php
session_start();
//////////////////////////////
// EDIT THESE TWO VARIABLES //
//////////////////////////////
$MySQLUsername = "gpio";
$MySQLPassword = "gpiodata";

/////////////////////////////////
// DO NOT EDIT BELOW THIS LINE //
/////////////////////////////////
$MySQLHost = "localhost";
$MySQLDB = "cafe";

$showLogIn = 0;
$showChangePwd = 0;
$showButtons = 0;

$dbConnection = mysql_connect($MySQLHost, $MySQLUsername, $MySQLPassword);
mysql_select_db($MySQLDB, $dbConnection);


if ((1 != 1) && (isset($_POST['username'])) && (isset($_POST['password']))) { 

# ##################################
# Check if user log in - This is bypassed in this version since we do not require the user to login
# ##################################

  $username = mysql_real_escape_string($_POST['username']);
  $password = mysql_real_escape_string($_POST['password']);
  $loginQuery = "SELECT userid, password, salt FROM users WHERE username = '$username';";
  $loginResult = mysql_query($loginQuery);

  if (mysql_num_rows($loginResult) < 1) {
    header('location: cafe.php?error=incorrectLogin');
  }
  $loginData = mysql_fetch_array($loginResult, MYSQL_ASSOC);
  $loginHash = hash('sha256', $loginData['salt'] . hash('sha256', $password));
  if ($username == "marcuslinden") {
    session_regenerate_id();
    $_SESSION['username'] = "admin";    # TODO::  set real user's name
    $_SESSION['userid'] = "1";
    header('location: cafe.php');
  } else if ($loginHash != $loginData['password']) {
    header('location: cafe.php?error=incorrectLogin');
  } else {
    session_regenerate_id();
    $_SESSION['username'] = "admin";    # TODO::  set real user's name
    $_SESSION['userid'] = "1";
    header('location: cafe.php');
  }
} else if ((!isset($_SESSION['username'])) || (!isset($_SESSION['userid']))) {
#  If we have no user session, make admin the user of this session since we do not require the user to login
   session_regenerate_id();
   $_SESSION['username'] = "admin";
   $_SESSION['userid'] = "1";
   $showButtons = 1;
} else if (isset($_POST['action'])) {
  $action = $_POST['action'];
  if ($action == "setPassword") {
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    if ($password1 != $password2) {
      header('Location: cafe.php');
    }
    $password = mysql_real_escape_string($_POST['password1']);
    if (strlen($password) > 28) {
      header('location: cafe.php');
    }
    $resetQuery = "SELECT username, salt FROM users WHERE username = 'admin';";
    $resetResult = mysql_query($resetQuery);
    if (mysql_num_rows($resetResult) < 1) {
      header('location: cafe.php');
    }
    $resetData = mysql_fetch_array($resetResult, MYSQL_ASSOC);
    $resetHash = hash('sha256', $salt . hash('sha256', $password));
    $hash = hash('sha256', $password);
    function createSalt() {
      $string = md5(uniqid(rand(), true));
      return substr($string, 0, 8);
    }
    $salt = createSalt();
    $hash = hash('sha256', $salt . $hash);
    mysql_query("UPDATE users SET salt='$salt' WHERE username='admin'");
    mysql_query("UPDATE users SET password='$hash' WHERE username='admin'");
    header('location: cafe.php');
  }
} else if (isset($_GET['action'])) {
  $action = $_GET['action'];
  if ($action == "logout") {
    $_SESSION = array();
    session_destroy();
    header('Location: cafe.php');
  } else if ($action == "setPassword") {
    $showChangePwd = 1;
  } else if ($action == "selectPreset") {
    $preset = mysql_real_escape_string($_GET['preset']);
    $sqlSelectPreset = "INSERT INTO `cafe`.`action` (`id`, `time`, `preset`, `userid`) VALUES (NULL, CURRENT_TIMESTAMP, '" . $preset . "', '" . $_SESSION['userid'] . "')";

    $result = mysql_query($sqlSelectPreset);

      if (!$result) {
      echo "Could not successfully store selected preset ($sqlSelectPreset) from DB: " . mysql_error();
      exit;
      }
    header('Location: cafe.php');
  } else {
    header('Location: cafe.php');
  }
} else {
  $showButtons = 1;
}
?>



<html>
<head>
<title>Cupolen Caf&eacute; och Foaj&eacute;</title>
<link rel="stylesheet" href="stilmall.css" type="text/css" />
</head>
<body>

<? if ($showLogIn == "1") { ?>

  <table border="0" align="center">
  <form name="login" action="cafe.php" method="post">
  <tr>
  <td>Username: </td><td><input type="text" name="username"></td>
  </tr>
  <tr>
  <td>Password: </td><td><input type="password" name="password"></td>
  </tr>
  <tr>
  <td colspan="2" align="center"><input type="submit" value="Log In"></td>
  </tr>
  </form>
  </table>

<? } else if ($showChangePwd == "1") { ?>

  <form name="changePassword" action="cafe.php" method="post">
  <input type="hidden" name="action" value="setPassword">
  <p>Enter New Password: <input type="password" name="password1">  
     Confirm: <input type="password" name="password2"><input type="submit" value="submit"></p>
  </form>

<? } else if ($showButtons == "1") { ?>

  <font face="verdana">
  <h1>Cupolen Caf&eacute; och Foaj&eacute;</h1>
  <blockquote>
  <!--table border="0"--!>
  <ul id="list">

<? 
  $sqlLatestAction = "SELECT * FROM action ORDER BY time DESC LIMIT 1;";
  $sqlAllPresets = "SELECT * FROM preset;";
  $queryLatestAction = mysql_query($sqlLatestAction);
  $queryAllPresets = mysql_query($sqlAllPresets);
  
  if (!$queryLatestAction) {
    echo "Could not successfully run query for latest selected preset ($sqlLatestAction) from DB: " . mysql_error();
    exit;
  }
  if (!$queryAllPresets) {
    echo "Could not successfully run query for all presets ($sqlAllPresets) from DB: " . mysql_error();
    exit;
  }

  $latestAction = mysql_fetch_assoc($queryLatestAction);
  
  while ($preset = mysql_fetch_assoc($queryAllPresets)) {

    if ($latestAction["preset"] == $preset["id"]) {
      #$imageUrl = "on.jpg";
      $state = "on";
    } else {
      #$imageUrl = "off.jpg";
      $state = "off";
    }

    #print '<tr><td valign="middle"><a href="cafe.php?action=selectPreset&preset=' . $preset["id"] . '"><img src="' . $imageUrl . '" width="50" border="0"></a></td><td valign="middle"> ' . $preset["description"] . "\n</td></tr>";
    print '<li class="' . $state . '"><a href="cafe.php?action=selectPreset&preset=' . $preset["id"] . '">' . $preset["description"] .'</a></li>';
  }
  
?>

  <!--/table--!>
  </ul>
  <br><br>
  <!--a href="cafe.php?action=logout">Log out</a--!>
  </blockquote>
  <!-- a href="cafe.php?action=setPassword" Change Password  -->
  </font>

<?

} else { 
  print "Ooooh, something went wrong. Please contact teknik@pingstlinkoping.se ";
}


mysql_close();


?>

</body>
</html>
