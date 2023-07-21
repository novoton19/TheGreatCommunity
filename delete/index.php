<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Page for confirmation of account deletion
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
	#Imports: $checkAuthorization, authorizeUser, unauthorizeUser
	require_once(__DIR__.'/../resources/functions/authorization.php');
	
	#First, we need to check whether the user is authorized or not
	list($_, $authorized, $_, $_) = $checkAuthorization();
	#Checking if user is authorized
	if (!$authorized)
	{
		#User is not authorized, therefore cannot delete account
		header('location: ../login');
		return;
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>
			The great community
		</title>
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
				Try deleting your account
			</span>
		</div>
		<!--Page content-->
		<div id="content">
			<!--Title-->
			<h1>
				Delete account
			</h1>
			<!--Message-->
			<p>
				Are you sure want to delete your account? This action cannot be undone!
			</p>
			<!--Action buttons-->
			<div>
				<!--Back to homepage-->
				<a class="button" href="../" title="Change mind">No, I changed my mind</a>
				<!--Delete account-->
				<a class="button" href="action.php" title="Delete account">Yes, I want to permanently delete my account</a>
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
		$_,
		$authorized
	);
?>