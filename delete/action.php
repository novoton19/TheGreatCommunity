<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Allows users to delete their account
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

	#At first, we need to check if the user is authorized or not and get their information
	list($_, $authorized, $_, $user) = $checkAuthorization();
	$userID = getValue($user, 'id');
	$username = getValue($user, 'username');
	$email = getValue($user, 'email');
	
	#Checking if user is authorized
	if (!$authorized)
	{
		#User is not logged in, nothing to delete here
		header('location: ../login');
		return;
	}
	#Unsetting unnecessary variables
	unset(
		$_,
		$authorized,
		$user
	);

	#Variables that will be used later in the script
	#Sql that will be used to delete user by id, username and email
	$deleteAccountSql = 'DELETE FROM users WHERE id = :id AND username = :username AND email = :email LIMIT 1';

	$time = time();
	$success = true;
	$reason = null;
	$result = [];

	#Attempting to delete user account
	try
	{
		#Deleting user
		list($success, $_, $_) = $executeSql($deleteAccountSql, [':id' => $userID, ':username' => $username, ':email' => $email]);
	}
	catch(Exception $e)
	{
		#Request did not succeed
		$success = false;
		$reason = 'Cannot delete user: '.$e->getMessage();
	}
	if ((!$success) and is_null($reason))
	{
		#Cannot crete user because the sql execution did not succeed
		$reason = 'User cannot be deleted now';
	}
	elseif ($success)
	{
		#User has been deleted
		$reason = 'User deleted successfully';
	}
	#Creating result
	$result = [
		'type' => 'register',
		'time' => $time,
		'expiration' => $time + 300,
		'success' => $success,
		'valid' => true,
		'reason' => $reason,
		'inputs' => [
			'username' => [
				'value' => $username
			],
			'email' => [
				'value' => $email,
			]
		]
	];
	#Adding result to the session
	$_SESSION['action'] = $result;

	header('location: ../register');
	#Unsetting all unnecessary variables
	unset(
		$userID,
		$username,
		$email,
		$deleteAccountSql,
		$time,
		$success,
		$reason,
		$result
	);
?>