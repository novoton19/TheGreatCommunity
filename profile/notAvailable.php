<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: When there is a problem with profile loading, users will end up here
	*/
	#Required scripts
	#Imports: getValue, getInputInfo
	require_once(__DIR__.'/../resources/functions/general.php');
	#Getting userID
	$userID = getValue($_GET, 'id');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>
			View profile in The great community
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
		<!--Page content-->
		<div id="content">
			<!--Title-->
			<h1>
				User not available
			</h1>
			<!--Message-->
			<p>
				<span> Oops! Looks like we couldn't load the profile you were looking for. </span>
				<span> You can </span>
				<!--Retry button-->
				<a class="button orange" href=".?id=<?= $userID; ?>"> try again </a>
				<span> in a few seconds </span>
			</p>
		</div>
		<!--Page footer-->
		<div id="footer">

		</div>
	</body>
</html>
<?php
	#Getting rid of all variables
	unset(
		$userID
	);
?>