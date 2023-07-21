<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: View profile page
	*/
	#Script must not be required by other scripts
	if (count(debug_backtrace(null, 1)))
	{
		#Script required by other script => do not proceed with execution
		echo 'File '.__FILE__.' cannot be included.';
		return;
	}
	session_start();

	#Required scripts
	#Imports: $db, $executeSql
	require_once(__DIR__.'/../resources/db.php');
	#Imports: getValue, getInputInfo
	require_once(__DIR__.'/../resources/functions/general.php');

	#Variables that will be used later in the script
	$getUserSql = 'SELECT username, email, registrationTime FROM users WHERE id = :id';

	$success = true;

	$e = null;
	$userID = null;
	$user = [];
	$userExists = false;
	$username = null;
	$email = null;
	$registrationTime = null;

	#Checking if $_GET exists
	if ($_GET)
	{
		#Getting userID
		$userID = getValue($_GET, 'id');
		#Trying to get user by id
		try
		{
			#Selecting user
			list($success, $user, $userExists) = $executeSql($getUserSql, [':id' => $userID], true);
		}
		catch(Exception $e)
		{
			#Request did not succeed
			$success = false;
			$reason = 'User cannot be found: '.$e->getMessage();
		}
		if ($success and $userExists)
		{
			#Getting username, email and registration time
			$username = getValue($user, 'username');
			$email = getValue($user, 'email');
			$registrationTime = date('Y-m-d H:i:s', getValue($user, 'registrationTime'));
		}
	}
	#Checking if request succeeded
	if (!$success)
	{
		#Request did not succeed
		header('location: notAvailable.php?id='.$userID);
		return;
	}
	elseif (!$userExists)
	{
		#User not found
		header('location: notFound.php?id='.$userID);
		return;
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>
			<?= $username; ?>'s profile in The great community
		</title>
		<!--Profile page css-->
		<link rel="stylesheet" type="text/css" href="style.css">
		<!--General css-->
		<link rel="stylesheet" type="text/css" href="../style.css">
		<!--Google icons-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	</head>
	<body>
		<!--Page header-->
		<div id="header">
			<?php require('../header.php'); ?>
		</div>
		<!--Tip-->
		<div class="tip">
			<i class="material-icons">
				info
			</i>
			<b>
				Tip:
			</b>
			<span>
				Try playing with with the profile ID in the URL. What would happen if it wasn't a number?
			</span>
		</div>
		<!--Page content-->
		<div id="content">
			<div id="profile">
				<!--Title-->
				<h1>
					<?= $username; ?>'s profile
				</h1>
				<!--Email-->
				<div class="email">
					<?= $email; ?>
				</div>
				<!--Registration date-->
				<div class="registrationTime">
					Member since: <?= $registrationTime; ?>
				</div>
			</div>
		</div>
		<!--Page footer-->
		<div id="footer">

		</div>
	</body>
</html>
<?php
	#Getting rid of all variables
	unset(
		$getUserSql,
		$success,
		$e,
		$userID,
		$user,
		$userExists,
		$username,
		$email,
		$registrationTime
	);
?>