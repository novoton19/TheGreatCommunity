<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Home page
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
	require_once(__DIR__.'/resources/db.php');
	#Imports: getValue, getInputInfo
	require_once(__DIR__.'/resources/functions/general.php');
	#Imports: $checkAuthorization, authorizeUser, unauthorizeUser
	require_once(__DIR__.'/resources/functions/authorization.php');
	
	#Header settings
	$headerSettings = [
		'homeLocation' => ''
	];

	#At first, we need to check if the user is authorized or not
	list($_, $authorized, $_, $user) = $checkAuthorization();
	$userID = getValue($user, 'id');
	$username = getValue($user, 'username');

	#Getting profiles
	list($_, $profiles, $_) = $executeSql('SELECT id, username, email FROM users ORDER BY registrationTime DESC LIMIT 5');
	#Getting profiles count
	list($_, $result, $_) = $executeSql('SELECT COUNT(id) AS profilesCount FROM users', [], true);
	$profilesCount = (int)getValue($result, 'profilesCount');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>
			The great community
		</title>
		<!--General css-->
		<link rel="stylesheet" type="text/css" href="style.css">
		<!--Google icons-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	</head>
	<body>
		<!--Page header-->
		<div id="header">
			<?php require('header.php'); ?>
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
				Try viewing someone's profile
			</span>
		</div>
		<!--Page content-->
		<div id="content">
			<!--Title-->
			<h1>
				Welcome to The great community
			</h1>
			<!--Description-->
			<p>
				The best community of people in the world!
			</p>
			<!--Page section-->
			<div id="profilesListWrapper">
				<!--Title-->
				<h2>
					Newest members
				</h2>
				<!--List of newest profiles-->
				<div id="profilesList">
					<?php if (empty($profiles)): ?>
						<!--There are no profiles in the website-->
						<div class="searchResult">
							No results
						</div>
					<?php else: ?>
						<!--Loading profiles-->
						<?php foreach ($profiles as $profileNum => $profile): ?>
							<!--Creating result-->
							<div class="searchResult">
								<!--Username-->
								<div class="username">
									<?= getValue($profile, 'username'); ?>
								</div>
								<!--Email-->
								<div class="email">
									<?= getValue($profile, 'email'); ?>
								</div>
								<!--Profile link-->
								<a class="button orange profileUrl" href="profile?id=<?= getValue($profile, 'id'); ?>">
									View profile
								</a>
							</div>
						<?php endforeach; ?>
						<!--Checking if there are more profiles than listed-->
						<?php if ($profilesCount > count($profiles)): ?>
							<!--Showing profiles count-->
							<div class="searchResult">
								And <?= $profilesCount - count($profiles); ?> more!
							</div>
						<?php endif; ?> 
					<?php endif; ?>
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
		$headerSettings,
		$_,
		$authorized,
		$user,
		$userID,
		$username,
		$profiles,
		$result,
		$profilesCount
	);
?>