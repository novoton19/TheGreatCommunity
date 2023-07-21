<?php
	/*
		DEVELOPER INFO
		Name: Novotny Ondrej
		Email: ondrej.novotny1410@gmail.com
		
		SCRIPT INFO
		Description: When there is a problem with settings page loading, user will end up here
	*/
	#Script must not be required by other scripts
	if (count(debug_backtrace(null, 1)))
	{
		#Script required by other script => do not proceed with execution
		echo 'File '.__FILE__.' cannot be included.';
		return;
	}
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
				Settings not available
			</h1>
			<!--Message-->
			<p>
				<span> Oops! Looks like we couldn't load the profile settings page. </span>
				<span> You can </span>
				<!--Retry button-->
				<a class="button orange"> try again </a>
				<span> in a few seconds </span>
			</p>
		</div>
		<!--Page footer-->
		<div id="footer">

		</div>
	</body>
</html>