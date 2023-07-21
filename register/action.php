<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Allows new users to register to the website
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
	#Imports: $checkUsername, $checkEmail, checkPassword, checkPasswordVerification
	require_once(__DIR__.'/../resources/functions/registrationCheck.php');
	#Imports: $checkAuthorization, authorizeUser, unauthorizeUser
	require_once(__DIR__.'/../resources/functions/authorization.php');

	#At first, we need to check if the user is authorized or not
	list($_, $authorized, $_, $user) = $checkAuthorization();
	$userID = getValue($user, 'id');
	#Checking if user is authorized
	if ($authorized)
	{
		#User is already logged in, therefore cannot register
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
	#Sql that will be used to create a new user
	$createUserSql = 'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)';

	$time = time();
	$success = true;
	$reason = null;
	$valid = false;
	
	$_ = $e = null;
	$username = $email = $password = $passwordVerification = null;
	$passwordHash = null;
	$usernameCheck = $emailCheck = $passwordCheck = $passwordVerificationCheck = [];
	$userID = null;
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
		$username = getValue($_POST, 'username');
		$email = getValue($_POST, 'email');
		$password = getValue($_POST, 'password');
		$passwordVerification = getValue($_POST, 'passwordVerification');

		#Checking inputs
		$usernameCheck = $checkUsername($username);
		$emailCheck = $checkEmail($email);
		$passwordCheck = checkPassword($password);
		$passwordVerificationCheck = checkPasswordVerification($password, $passwordVerification);

		#Getting status
		$success = ($usernameCheck[0] and $emailCheck[0]);
		#Checking if success
		if (!$success)
		{
			#One of the checks did not succeed
			$reason = 'Request did not succeed';
		}
		else
		{
			#Getting status
			$valid = ($usernameCheck[1] and $emailCheck[1] and $passwordCheck[0] and $passwordVerificationCheck[0]);
			#Checking if all of the inputs were valid
			if (!$valid)
			{
				#One of the inputs were invalid => cannot proceed with registration
				#Do not give reason to the entire form, reason to each invalid input will be enough
				$reason = null;
			}
			else
			{
				#Inputs were valid, we can proceed to account creation
				#Hashing password
				$passwordHash = password_hash($password, PASSWORD_DEFAULT);
				#Attempting to create user
				try
				{
					#Creating user
					list($success, $_, $_) = $executeSql($createUserSql, [':username' => $username, ':email' => $email, ':password' => $passwordHash]);
				}
				catch(Exception $e)
				{
					#Request did not succeed
					$success = false;
					$reason = 'User cannot be registered: '.$e->getMessage();
				}
				if ((!$success) and is_null($reason))
				{
					#Cannot crete user because the sql execution did not succeed
					$reason = 'User cannot be registered now';
				}
				elseif ($success)
				{
					#User registered successfully
					#Getting last insert id
					$userID = $db->lastInsertId();
					$reason = 'Registration successful';
					#Automatically authorizing user
					authorizeUser($userID);
				}
			}
		}
	}
	#Creating result
	$result = [
		'type' => 'register',
		'time' => $time,
		'expiration' => $time + 300,
		'success' => $success,
		'valid' => $valid,
		'reason' => $reason,
		'inputs' => [
			'username' => [
				'value' => $username,
				'valid' => $usernameCheck[1],
				'reason' => $usernameCheck[2]
			],
			'email' => [
				'value' => $email,
				'valid' => $emailCheck[1],
				'reason' => $emailCheck[2]
			],
			'password' => [
				'valid' => $passwordCheck[0],
				'reason' => $passwordCheck[1]
			],
			'passwordVerification' => [
				'valid' => $passwordVerificationCheck[0],
				'reason' => $passwordVerificationCheck[1]
			]
		]
	];
	#Adding result to the session
	$_SESSION['action'] = $result;

	
	#Checking if request succeeded
	if (!($success and $valid))
	{
		#Return back to registration form
		header('location: .');
	}
	else
	{
		#Go to profile page
		header('location: ../profile?id='.$userID);
	}
	#Unsetting all unnecessary variables
	unset(
		$createUserSql,
		$time,
		$success,
		$reason,
		$valid,
		$_,
		$e,
		$username,
		$email,
		$password,
		$passwordVerification,
		$passwordHash,
		$usernameCheck,
		$emailCheck,
		$passwordCheck,
		$passwordVerificationCheck,
		$userID,
		$result
	);
?>