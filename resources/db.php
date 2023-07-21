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
	require_once(__DIR__.'/functions/general.php');
	

	#Database login information
	define('DB_NAME', 'ukazka');
	define('DB_USER', 'admin');
	define('DB_PASSWORD', 'admin');
	define('DB_HOST', 'localhost');

	#Creating database connection
	$db = new PDO(
		'mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD,
		[
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		]
	);
	#Executes sql query and returns result
	#If $returnSingleResult is true, the function will get the first result from the array of results and return it
	#Function always returns an array with these values:
	#$success - whether request succeeded
	#$result - the result of the query (array)
	#$resultExists - whether the $result has any items or not
	$executeSql = function($sql, $data = [], $returnSingleResult = false) use ($db)
	{
		#Preparing sql
		$toExecute = $db->prepare($sql);
		#Executing sql
		$success = $toExecute->execute($data);
		$error = !$success;

		#Getting result
		/*
		Example result:
		[
			0 => ["id" => "6", "name" => "John Doe"],
			1 => ["id" => "15", "name" => "Barrack Obama"]
		]
		*/
		$result = $toExecute->fetchAll(PDO::FETCH_ASSOC);

		#Getting result info
		$resultExists = !empty($result);
		$isSingleResult = count($result) == 1;

		#Checking if we want to return only one result
		if ($success and $resultExists and $returnSingleResult) {
			#Getting single result
			/*Example result:
			["id" => "6", "name" => "John Doe"]
			*/
			$result = getValue(
				$result,
				0,
				[]
			);
		}
		#Returning result information
		return [
			$success,
			$result,
			$resultExists
		];
	};
?>