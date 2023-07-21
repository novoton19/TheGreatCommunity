<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Settings page for existing users
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

	#Header settings
	$headerSettings = [
		'keepUsername' => true
	];
	
	#At first, we need to check if the user is authorized or not and get their information
	list($success, $authorized, $_, $user) = $checkAuthorization();
	#Getting user information
	$currentUsername = getValue($user, 'username');
	$currentEmail = getValue($user, 'email');
	$currentPassword = '••••••••';

	#Getting information from previous request (if the user already sent the form, but some of the inputs were incorrect, there will be response)
	$action = getValue($_SESSION, 'action', []);
	$inputs = getValue($action, 'inputs', []);
	$actionType = getValue($action, 'type', '', true);
	$expiration = getValue($action, 'expiration', time());
	$formValid = getValue($action, 'valid', false);

	#Predefined input values (i.e. from previous unsuccessful requests)
	$username = $currentUsername;
	$email = $currentEmail;
	#Reasons of each input to let user know what's the problem
	$formReason = $usernameReason = $emailReason = $passwordReason = $passwordVerificationReason = null;
	#Class for each input (the class might be "valid" if the input was correctly filled)
	$usernameInputClass = $emailInputClass = $passwordInputClass = $passwordVerificationInputClass = null;
	#Class for each label containing reason (might be "hidden" if there is no reason for that input)
	$formReasonClass = $usernameReasonClass = $emailReasonClass = $passwordReasonClass = $passwordVerificationReasonClass = 'hidden';
	
	#Checking actionType
	if ($actionType == 'update' and $expiration > time())
	{
		#Loading form reason
		$formReason = getValue($action, 'reason', '');
		$formReasonClass = empty($formReason) ? 'hidden' : ($formValid ? 'valid' : 'invalid');
		
		#Loading all input information
		list($username, $usernameReason, $usernameInputClass, $usernameReasonClass) = getInputInfo($inputs, 'username');
		list($email, $emailReason, $emailInputClass, $emailReasonClass) = getInputInfo($inputs, 'email');
		list($_, $passwordReason, $passwordInputClass, $passwordReasonClass) = getInputInfo($inputs, 'password');
		list($_, $passwordVerificationReason, $passwordVerificationInputClass, $passwordVerificationReasonClass) = getInputInfo($inputs, 'passwordVerification');
	}
	
	#Checking if user is authorized
	if (!$authorized)
	{
		header('location: ../login');
		return;
	}
	#Checking if account obtained successfully
	if (!$success)
	{
		header('location: notFound.php');
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
				Try updating your password
			</span>
		</div>
		<!--Page content-->
		<div id="content">
			<!--Title-->
			<h1>
				Account settings
			</h1>
			<!--Page section-->
			<div>
				<!--Section title-->
				<h2>
					Update account
				</h2>
				<!--Update information form-->
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
					<!--Username input-->
					<div class="inputWrapper <?= $usernameInputClass; ?>">
						<label for="username">
							Username (<?= $currentUsername; ?>)
						</label>
						<input id="username" type="text" name="username" placeholder="New username" required="required" value="<?= $username; ?>">
						<label for="username" class="reason <?= $usernameClass; ?>">
							<?= $usernameReason; ?>
						</label>
					</div>
					<!--Email input-->
					<div class="inputWrapper <?= $emailInputClass; ?>">
						<label for="email">
							Email address (<?= $currentEmail; ?>)
						</label>
						<input id="email" type="email" name="email" placeholder="New email" required="required" value="<?= $email; ?>">
						<label for="email" class="reason <?= $emailClass; ?>">
							<?= $emailReason; ?>
						</label>
					</div>
					<!--Password input-->
					<div class="inputWrapper <?= $passwordInputClass; ?>">
						<label for="password">
							Password (<?= $currentPassword; ?>)
						</label>
						<input id="password" type="password" name="password" placeholder="New password" required="required">
						<label for="password" class="reason <?= $passwordClass; ?>">
							<?= $passwordReason; ?>
						</label>
					</div>
					<!--Password verification input-->
					<div class="inputWrapper <?= $passwordVerificationInputClass; ?>">
						<input id="passwordVerification" type="password" name="passwordVerification" placeholder="Re-type your new password" required="required">
						<label for="passwordVerification" class="reason <?= $passwordVerificationClass; ?>">
							<?= $passwordVerificationReason; ?>
						</label>
					</div>
					<!--Update button-->
					<div class="inputWrapper">
						<input type="submit" name="submitButton" value="Update information">
					</div>
				</form>
			</div>
			<!--Page section-->
			<div>
				<!--Section title-->
				<h2>
					Other actions
				</h2>
				<!--Log out button-->
				<a class="button" href="../logout.php" title="Log out">
					Log out
				</a>
				<!--Delete account button-->
				<a class="button" href="../delete" title="Delete account">
					Delete account
				</a>
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
		$success,
		$authorized,
		$_,
		$user,
		$currentUsername,
		$currentEmail,
		$currentPassword,
		$action,
		$inputs,
		$actionType,
		$expiration,
		$formValid,
		$username,
		$currentUsername,
		$email,
		$currentEmail,
		$formReason,
		$usernameReason,
		$emailReason,
		$passwordReason,
		$passwordVerificationReason,
		$usernameInputClass,
		$emailInputClass,
		$passwordInputClass,
		$passwordVerificationInputClass,
		$formReasonClass,
		$usernameReasonClass,
		$emailReasonClass,
		$passwordReasonClass,
		$passwordVerificationReasonClass
	);
?>