<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Login page for existing users
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
	#Imports: getValue, getInputInfo
	require_once(__DIR__.'/../resources/functions/general.php');
	#Imports: $checkAuthorization, authorizeUser, unauthorizeUser
	require_once(__DIR__.'/../resources/functions/authorization.php');
	
	#At first, we need to check if the user is authorized or not
	list($_, $authorized, $_, $user) = $checkAuthorization();
	#Checking if user is authorized	
	if ($authorized)
	{
		#User is already logged in, therefore cannot login
		header('location: ../profile?id='.getValue($user, 'id'));
		return;
	}
	
	#Getting search term
	$searchTerm = getValue($_GET, 's');
	
	#Getting information from previous request (if the user already sent the form, but some of the inputs were incorrect, there will be response)
	$action = getValue($_SESSION, 'action', []);
	$inputs = getValue($action, 'inputs', []);
	$actionType = getValue($action, 'type', '', true);
	$expiration = getValue($action, 'expiration', time());
	$formValid = getValue($action, 'valid');
	$formReason = null;
	$formReasonClass = 'hidden';

	#Predefined input values (i.e. from previous unsuccessful requests)
	$usernameOrEmail = null;
	#Class for each input (the class might be "valid" if the input was correctly filled)
	$usernameOrEmailInputClass = $passwordInputClass = null;
	
	#Checking actionType
	if ($actionType == 'login' and $expiration > time())
	{
		#Loading form reason
		$formReason = getValue($action, 'reason', '');
		$formReasonClass = empty($formReason) ? 'hidden' : (is_null($formValid) ? '' : ($formValid ? 'valid' : 'invalid'));
		#Loading all input information
		list($usernameOrEmail, $_, $usernameOrEmailInputClass, $_) = getInputInfo($inputs, 'usernameOrEmail');
		list($_, $_, $passwordInputClass, $_) = getInputInfo($inputs, 'password');
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>
			Log in to The great community
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
				Try logging in as user that is not yet registered
			</span>
		</div>
		<!--Page content-->
		<div id="content">
			<!--Title-->
			<h1>
				Log in
			</h1>
			<!--Log in form-->
			<form action="action.php" method="post">
				<!--Message with icon regarding to the whole form-->
				<div class="formReason <?= $formReasonClass; ?>">
					<!--Icon-->
					<i class="material-icons">
						error
					</i>
					<!--Message-->
					<div>
						<?= $formReason; ?>
					</div>
				</div>
				<!--Username or email input-->
				<div class="inputWrapper <?= $usernameOrEmailInputClass; ?>">
					<label for="usernameOrEmail">
						Username or email
					</label>
					<input id="usernameOrEmail" type="text" name="usernameOrEmail" placeholder="Username or email" required="required" value="<?= $usernameOrEmail; ?>">
				</div>
				<!--Password input-->
				<div class="inputWrapper <?= $passwordInputClass; ?>">
					<label for="password">
						Password
					</label>
					<input id="password" type="password" name="password" placeholder="Password" required="required">
				</div>
				<!--Log in button-->
				<div class="inputWrapper">
					<input type="submit" name="submitButton" value="Log in">
				</div>
			</form>
		</div>
		<!--Page footer-->
		<div id="footer">

		</div>
	</body>
</html>
<?php
	#Getting rid of all variables
	unset(
		$user,
		$action,
		$inputs,
		$actionType,
		$expiration,
		$formReason,
		$formReasonClass,
		$usernameOrEmail,
		$usernameOrEmailInputClass,
		$formReason,
		$formReasonClass
	);
?>