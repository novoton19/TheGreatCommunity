<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Logs out current user
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
	#Imports: $checkAuthorization, authorizeUser, $unauthorizeUser
	require_once(__DIR__.'/resources/functions/authorization.php');
	
	#Unauthorizing user
	$unauthorizeUser();
	#Redirect to login page
	header('location: login');
?>