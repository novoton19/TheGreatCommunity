<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Allows new users to register to the website
	*/
	#Script must be required by other scripts, cannot run on its own
	if (!count(debug_backtrace(null, 1)))
	{
		#Script is not required by any other scripts => do not proceed with execution
		echo 'This script cannot run independently.';
		return;
	}
	#session_start(); #<--This will be in the file that required this script

	#Required scripts
	#Imports: $db, $executeSql
	require_once(__DIR__.'/../db.php');
	#Imports: getValue
	require_once(__DIR__.'/general.php');

	#Checks if user is authorized
	#Returns following information in this order:
	#Whether request succeeded
	#Whether user is authorized
	#Reason why user is not authorized/if not
	#User information as array
	$checkAuthorization = function() use ($executeSql)
	{
		#Sql to select user by id
		$getUserSql = 'SELECT id, username, email, registrationTime FROM users WHERE id = :id';
		
		#Whether the user is authorized
		$success = true;
		$authorized = false;
		$reason = null;
		$account = [];

		#Getting login information
		$loginInfo = getValue($_SESSION, 'login', []);
		$approval = getValue($loginInfo, 'approval');
		$expiration = getValue($loginInfo, 'expiration');
		$userID = getValue($loginInfo, 'id');

		#Checking approval and expiration times
		if (gettype($approval) != 'integer' or gettype($expiration) != 'integer')
		{
			#Cannot verify expiration
			$reason = 'Session expired';
		}
		elseif ($expiration <= time())
		{
			#Session expired
			$reason = 'Session expired';
		}
		else
		{
			#Trying to get user information
			try
			{
				#Getting user
				list($success, $account, $accountExists) = $executeSql($getUserSql, [':id' => $userID], true);
			}
			catch(Exception $e)
			{
				#Request did not succeed
				$success = false;
				$reason = 'Authorized user cannot be found: '.$e->getMessage();
			}
			if ((!$success) and is_null($reason))
			{
				#Cannot crete user because the sql execution did not succeed
				$reason = 'Authorized user cannot be found';
			}
			elseif ($success)
			{
				#Checking if user exists
				if (!$accountExists)
				{
					#User not found in the database
					$reason = 'Authorized user doesn\'t exist';
				}
				else
				{
					#User obtained successfully
					$authorized = true;
				}
			}
		}
		return [
			$success,
			$authorized,
			$reason,
			$account
		];
	};
	#Authorizes user with given id
	function authorizeUser($id, $duration = 3600)
	{
		#Authorizing user
		$_SESSION['login'] = [
			'approval' => time(),
			'expiration' => $duration + time(),
			'id' => $id
		];
	}
	#Unauthorizes user - will no longer be logged in
	$unauthorizeUser = function() use ($checkAuthorization)
	{
		#Getting authorization status
		list($_, $authorized, $_, $user) = $checkAuthorization();
		$username = getValue($user, 'username');
		#Current time
		$time = time();

		#Checking if user was authorized
		if ($authorized)
		{
			#Unauthorizing user by setting expiration to current time
			$_SESSION['login']['expiration'] = $time;
		}
		#Adding action to the session
		$_SESSION['action'] = [
			'type' => 'login',
			'time' => $time,
			'expiration' => $time + 300,
			'success' => true,
			'valid' => true,
			'reason' => 'Successfuly logged out',
			'inputs' => [
				'usernameOrEmail' => [
					'value' => $username,
					'valid' => null
				],
				'password' => [
					'valid' => null,
				]
			]
		];
	};
?>