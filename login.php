<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"?
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=win1250" />
<title>Apl Anmelden/prihlaseni</title>
<?php
//Normally your username and pass would be stored in a database.
//For this example you will assume that you have already retrieved them.
$GLOBALS['user'] = "jr";
$GLOBALS['pass'] = "jr";
//Now, check if you have a valid submission.
if (isset ($_POST['user']) && isset ($_POST['pass'])){
	//Then check to see if you have a match.
	if (strcmp ($_POST['user'], $GLOBALS['user']) == 0 && strcmp ($_POST['pass'], $GLOBALS['pass']) == 0){
	//If you have a valid match, then set the sessions.
	$_SESSION['user'] = $_POST['user'];
	$_SESSION['pass'] = $_POST['pass'];
} else {
?><div align="center"><p style="color: #FF0000;">?
Sorry, you have entered an incorrect login.</p></div><?php
}
}
//Check if you need to logout.
if ($_POST['logout'] == "yes"){
unset ($_SESSION['user']);
unset ($_SESSION['pass']);
session_destroy();
}
//You then use this function on every page to check for a valid login at all
//times.
function checkcookies () 
{
if (strcmp ($_SESSION['user'], $GLOBALS['user']) == 0 && strcmp ($_SESSION['pass'], $GLOBALS['pass']) == 0)
{
	return true;
} 
else 
{
	return false;
}
}
?>
</head>
<body>
<div align="center">
<?php
//Check if you have a valid login.
if (checkcookies()){
?>

<p>Congratulations, you are logged in!</p>
<form action="login.php" method="post" style="margin: 0px;">
<input type="hidden" name="logout" value="yes" />
<input type="submit" value="Logout" />
</form>
<?php
//Or else present a login form.
} else {
?>
<form action="login.php" method="post" style="margin: 0px;">
<div style="width: 500px; margin-bottom: 10px;">
<div style="width: 35%; float: left; text-align: left;">
Username:
</div>
<div style="width: 64%; float: right; text-align: left;">
<input type="text" name="user" maxlength="25" />
</div>
<br style="clear: both;" />
</div>
<div style="width: 500px; margin-bottom: 10px;">
<div style="width: 35%; float: left; text-align: left;">
Password:
</div>
<div style="width: 64%; float: right; text-align: left;">
<input type="password" name="pass" maxlength="25" />
</div>
<br style="clear: both;" />
</div>
<div style="width: 500px; text-align: left;">?
<input type="submit" value="Login" /></div>
</form>
<?php
}
?>
</div>
</body>
</html>