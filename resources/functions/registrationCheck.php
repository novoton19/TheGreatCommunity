<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Database connection
	*/
	#Script must be required by other scripts, cannot run on its own
	if (!count(debug_backtrace(null, 1)))
	{
		#Script is not required by any other scripts => do not proceed with execution
		echo 'This script cannot run independently.';
		return;
	}
	#Getting important functions
	#Imports: getValue
	require_once(__DIR__.'/general.php');
	#Imports: $db, $executeSql
	require_once(__DIR__.'/../db.php');


	#Checks username availability
	#Returns following information: whether the username check was successful, if the username is valid and the reason why it might not be valid
	$checkUsername = function($username, $allowedUsername = null) use ($executeSql)
	{
		#Minimum and maximum username length
		$minLength = 3;
		$maxLength = 24;
		#Characters that may be in the username
		$allowedCharacters = '([a-zA-Z0-9])';
		#Sql to select user by username
		$sql = 'SELECT id FROM users WHERE username = :username LIMIT 1';

		#Result preset
		$success = true;
		$valid = false;
		$reason = null;
		
		#Checking username type
		if (is_null($username))
		{
			#Username not specified
			$reason = 'Username is required';
		}
		elseif (gettype($username) != 'string')
		{
			#Username is not a string, not valid
			$reason = 'Must be a string';
		}
		else
		{
			#Getting length of username
			$length = strlen($username);
			#Checking length
			if ($length < $minLength)
			{
				#Too short
				$reason = 'Must have at least '.$minLength.' characters';
			}
			elseif ($length > $maxLength)
			{
				#Too long
				$reason = 'Must not have over '.$maxLength.' characters';
			}
			else
			{
				#Checking characters
				if (preg_match_all($allowedCharacters, $username) != $length)
				{
					#Contains invalid characters
					$reason = 'Username may contain english characters and numbers only';
				}
				elseif ($username == $allowedUsername)
				{
					#User is allowed to have this username
					$valid = true;
				}
				else
				{
					#Trying to find user by username
					try
					{
						#Getting user
						list($success, $user, $userExists) = $executeSql($sql, [':username' => $username], true);
					}
					catch (Exception $e) {
						#Request did no succeed
						$success = false;
						$reason = 'Username cannot be verified: '.$e->getMessage();
					}
					#Checking if user exists
					if ($success and $userExists)
					{
						#That username is already in use
						$reason = 'Username is already in use';
					}
					elseif ((!$success) and is_null($reason))
					{
						#Cannot verify availability because the sql execution did not succeed
						$reason = 'Username cannot be verified';
					}
					else
					{
						#Valid username
						$valid = true;
					}
				}
			}
		}
		#Giving result
		return [
			$success,
			$valid,
			$reason
		];
	};
	#Checks email availability
	#Returns following information: whether the email check was successful, if the email is valid and the reason why it might not be valid
	$checkEmail = function($email, $allowedEmail = null) use ($executeSql)
	{
		#Sql to select user by email
		$sql = 'SELECT id FROM users WHERE email = :email LIMIT 1';

		#Result preset
		$success = true;
		$valid = false;
		$reason = null;
		
		#Checking email type
		if (is_null($email))
		{
			#Email not specified
			$reason = 'Email is required';
		}
		elseif (gettype($email) != 'string')
		{
			#Email is not a string, not valid
			$reason = 'Must be a string';
		}
		else
		{
			#Checking email
			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$reason = 'Not an email';
			}
			elseif ($email == $allowedEmail)
			{
				#User is allowed to have this username
				$valid = true;
			}
			else
			{
				#Trying to find user by email
				try
				{
					#Getting user
					list($success, $user, $userExists) = $executeSql($sql, [':email' => $email], true);
				}
				catch (Exception $e) {
					#Request did no succeed
					$success = false;
					$reason = 'Email cannot be verified: '.$e->getMessage();
				}
				#Checking if user exists
				if ($success and $userExists)
				{
					#That email is already in use
					$reason = 'That email is already registered';
				}
				elseif ((!$success) and is_null($reason))
				{
					#Cannot verify availability because the sql execution did not succeed
					$reason = 'Email cannot be verified';
				}
				else
				{
					#Valid email
					$valid = true;
				}
			}
		}
		#Giving result
		return [
			$success,
			$valid,
			$reason
		];
	};
	#Checks password
	#Returns following information: if the password is valid and the reason why it might not be valid
	function checkPassword($password)
	{
		#Minimum password length
		$minLength = 8;
		
		#Result preset
		$valid = false;
		$reason = null;

		#Checking password type
		if (is_null($password))
		{
			#Password not specified
			$reason = 'Password is required';
		}
		elseif (gettype($password) != 'string')
		{
			#Password is not a string
			$reason = 'Must be a string';
		}
		else
		{
			#Getting length of password
			$length = strlen($password);
			#Checking length
			if ($length < $minLength)
			{
				#Too weak
				$reason = 'Password must have at least '.$minLength.' characters';
			}
			else
			{
				#Checking strength 
				#https://stackoverflow.com/questions/3937569/preg-match-special-characters
				#Special chars []{}()<>#£$%&@*!?+-~/\\|"\':;.,=_¬`
				if (
					!preg_match('/[a-z]/', $password) or
					!preg_match('/[A-Z]/', $password) or
					!preg_match('/[0-9]/', $password) or
					!preg_match('/['.preg_quote('[]{}()<>#£$%&@*!?+-~/\\\\|"\\\':;.,=_¬`', '/').']/', $password))
				{
					#Password is too weak
					$reason = 'Password must contain at least one uppercase and lowercase character, number and a special character';	
				}
				else
				{
					$valid = true;
				}
			}
		}
		return [
			$valid,
			$reason
		];
	}
	#Checks password verification
	#Returns following information: if the password is valid and the reason why it might not be valid
	function checkPasswordVerification($password, $passwordVerification)
	{
		#Result preset
		$valid = false;
		$reason = null;

		#Checking password type
		if (is_null($passwordVerification))
		{
			#Password not specified
			$reason = 'Password verification is required';
		}
		elseif (gettype($passwordVerification) != 'string')
		{
			#Password is not a string
			$reason = 'Must be a string';
		}
		else
		{
			#Checking if passwords match
			if ($password != $passwordVerification)
			{
				#Passwords do not match
				$reason = 'Passwords do not match';
			}
			else
			{
				$valid = true;
			}
		}
		return [
			$valid,
			$reason
		];
	}
?>