<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Allows returning users to log in
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
	#Imports: getValue
	require_once(__DIR__.'/../resources/functions/general.php');
	#Imports: $checkAuthorization, authorizeUser, unauthorizeUser
	require_once(__DIR__.'/../resources/functions/authorization.php');

	#At first, we need to check if the user is authorized or not
	list($_, $authorized, $_, $user) = $checkAuthorization();
	$userID = getValue($user, 'id');
	#Checking if user is logged in
	if ($authorized)
	{
		#User is already logged in, therefore cannot login again
		header('location: ../profile?id='.$userID);
		return;
	}
	#Unsetting unnecessary variables
	unset(
		$_,
		$authorized,
		$user,
		$userID
	);

	#Variables that will be used later in the script
	#Sql that will be used to select user by username or email
	$getUserSql = 'SELECT id, password FROM users WHERE username = :usernameOrEmail or email = :usernameOrEmail Limit 1';

	$time = time();
	$success = true;
	$reason = null;
	$valid = false;
	
	$_ = $e = null;
	$usernameOrEmail = $password = null;
	$passwordHash = null;
	$userID = null;
	$user = [];
	$userExists = false;
	$result = [];

	#Checking if $_POST exists
	if (!$_POST)
	{
		#$_POST doesn't exist
		$reason = 'Missing $_POST';
	}
	else
	{
		#Getting inputs
		$usernameOrEmail = getValue($_POST, 'usernameOrEmail', '');
		$password = getValue($_POST, 'password', '');

		#Attempting to select user
		try
		{
			#Selecting user
			list($success, $user, $userExists) = $executeSql($getUserSql, [':usernameOrEmail' => $usernameOrEmail], true);
		}
		catch(Exception $e)
		{
			#Request did not succeed
			$success = false;
			$reason = 'User cannot be found: '.$e->getMessage();
		}
		if ((!$success) and is_null($reason))
		{
			#Cannot verify login because the sql execution did not succeed
			$reason = 'User cannot be found now';
		}
		elseif ($success)
		{
			#Checking if user exists
			if (!$userExists)
			{
				#User not found
				$reason = 'Invalid credentials';
			}
			else
			{
				#Getting id and passwordHash
				$userID = getValue($user, 'id');
				$passwordHash = getValue($user, 'password');

				#Checking password
				if (!password_verify($password, $passwordHash))
				{
					#Invalid password
					$reason = 'Invalid credentials';
				}
				else
				{
					#Password is correct
					$valid = true;
					$reason = 'Login successful';
					#Authorizing user
					authorizeUser($userID);
				}
			}
		}
	}
	#Creating result
	$result = [
		'type' => 'login',
		'time' => $time,
		'expiration' => $time + 300,
		'success' => $success,
		'valid' => $valid,
		'reason' => $reason,
		'inputs' => [
			'usernameOrEmail' => [
				'value' => $usernameOrEmail,
				'valid' => $valid
			],
			'password' => [
				'valid' => $valid,
			]
		]
	];
	#Adding result to the session
	$_SESSION['action'] = $result;

	#Checking if request succeeded
	if (!($success and $valid))
	{
		#Return back to login form
		header('location: .');
	}
	else
	{
		#Go to profile page
		header('location: ../profile?id='.$userID);
	}
	#Unsetting all unnecessary variables
	unset(
		$getUserSql,
		$time,
		$success,
		$reason,
		$valid,
		$_,
		$e,
		$usernameOrEmail,
		$password,
		$passwordHash,
		$userID,
		$user,
		$userExists,
		$result
	);
?>