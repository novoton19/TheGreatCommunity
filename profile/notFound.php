<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: The users that searched for profiles that don't exist are redirected here
	*/
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
		<!--Tip-->
		<div class="tip">
			<i class="material-icons">
				info
			</i>
			<b>
				Tip:
			</b>
			<span>
				↑ Try searching someone else! ↑
			</span>
		</div>
		<!--Page content-->
		<div id="content">
			<!--Title-->
			<h1>
				User not found
			</h1>
			<!--Message-->
			<p>
				<span> Oops! Looks like we cannot find the user you're looking for. </span>
				<span> You can </span>
				<!--Retry button-->
				<a class="button orange" href=".?id=<?= $userID; ?>"> try again </a>
				<span> in a few seconds </span>
			</p>
			<!--Message-->
			<p>
				<span> Alternatively, you can </span>
				<!--Search profiles link-->
				<a class="button orange" href="../search/">
					try searching
				</a>
				<span> for them by their username </span>
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