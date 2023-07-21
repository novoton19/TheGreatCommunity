<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Script that shows page header
	*/
	#Script must be required by other scripts
	if (!count(debug_backtrace(null, 1)))
	{
		#Script is not required by other script => do not proceed with execution
		echo 'File '.__FILE__.' cannot run independently.';
		return;
	}
	#Whether session was running when the script started
	$sessionActive = (session_status() == PHP_SESSION_ACTIVE);
	#Checking if session is active
	if (!$sessionActive)
	{
		#Starting session
		session_start();
	}

	#Required scripts
	#Imports: getValue, getInputInfo
	require_once(__DIR__.'/resources/functions/general.php');
	#Imports: $checkAuthorization, authorizeUser, unauthorizeUser
	require_once(__DIR__.'/resources/functions/authorization.php');

	#Getting authorization status and user information (in order to show correct information in header)
	list($_, $authorized, $_, $currentUser) = $checkAuthorization();
	$currentUserID = getValue($currentUser, 'id');
	$currentUsername = getValue($currentUser, 'username');

	#Header settings
	$includeSearchForm = true;
	$keepUsername = false;
	$homeLocation = '../';
	#Checking if settings exists
	if (isset($headerSettings))
	{
		$includeSearchForm = getValue($headerSettings, 'includeSearchForm', true);
		$keepUsername = getValue($headerSettings, 'keepUsername', false);
		$homeLocation = getValue($headerSettings, 'homeLocation', '../');
	}

	#Getting search term
	$searchTerm = getValue($_GET, 's');
?>
<!--The great community logo with a link to home page-->
<a href="..">
	<img class="logo" src="<?= $homeLocation; ?>assets/logo.png" alt="The great community logo" title="The great community">
</a>
<?php if ($includeSearchForm): ?>
	<!--Search form for easy account lookup-->
	<form class="searchForm" action="<?= $homeLocation; ?>search" method="get">
		<!--Search input-->
		<div class="inputWrapper">
			<input id="searchTerm" type="text" name="s" placeholder="Search profile" minlength="3" value="<?= $searchTerm; ?>">
		</div>
		<!--Search button with icon-->
		<div class="inputWrapper">
			<button>
				<i class="material-icons">
					search
				</i>
			</button>
		</div>
	</form>
<?php endif; ?>
<!--Account actions (based on whether the user is logged in or not)-->
<div>
	<?php if ($authorized): ?>
		<!--User is logged in-->
		<!--Profile link-->
		<a class="button" href="<?= $homeLocation; ?>profile?id=<?= $currentUserID; ?>" title="View profile">
			Logged in as <?= $currentUsername; ?>
		</a>
		<!--Settings link with icon-->
		<a class="button" href="<?= $homeLocation; ?>settings" title="Settings">
			<i class="material-icons">
				settings
			</i>
		</a>
		<!--Log out link with icon-->
		<a class="button" href="<?= $homeLocation; ?>logout.php" title="Log out">
			<i class="material-icons">
				logout
			</i>
		</a>
	<?php else: ?>
		<!--User isn't logged in-->
		<!--Register page link-->
		<a class="button" href="<?= $homeLocation; ?>register">
			Register
		</a>
		<!--Login page link-->
		<a class="button" href="<?= $homeLocation; ?>login">
			Login
		</a>
	<?php endif; ?>
</div>
<?php
	#Getting rid of all variables
	unset(
		$sessionActive,
		$_,
		$authorized,
		$currentUser,
		$currentUserID,
		#$searchTerm #<--Not necessary
	);
	#Checking if should keep username or not
	if (!$keepUsername)
	{
		#Unset variable
		unset(
			$currentUsername
		);
	}
?>