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

	#Returns $value which is stored under the specific $key in given $array
	#If $forceSameType is true and gettype($value) is not equal to gettype($defaultValue), $defaultValue is returned
	#If $defaultValue is null, the type of $value is not being checked
	#If $value doesn't exist, function returns $defaultValue
	function getValue($array, $key, $defaultValue = null, $forceSameType = false)
	{
		#Checking arguments
		#$array must be an array
		if (gettype($array) != 'array')
		{
			#Not an array
			return $defaultValue;
		}
		#$key must not be null
		if (is_null($key))
		{
			#Key is nyll
			return $defaultValue;
		}
		
		#Checking if value exists
		if (!isset($array[$key]))
		{
			#Value doesn't exist
			return $defaultValue;
		}
		#Getting value
		$value = $array[$key];
		#If default value is specified and we are forced to return same type, we need to make additional check
		if ($forceSameType and !is_null($defaultValue))
		{
			#Checking value type
			if (gettype($value) != gettype($defaultValue))
			{
				#Value type doesn't match with the type of default value => return default value
				return $defaultValue;
			}
		}
		#Value obtained without any problems
		return $value;
	}
	#Returns input information based on predefined structure
	#Accepts list of input informations and input name
	#Returns input value, reason and proper class
	#Predefined structure: ["inputName" => ["value" => $value, "valid" => $valid, "reason" => $reason]]
	function getInputInfo($inputsList, $inputName)
	{
		#Getting input information
		$inputInfo = getValue($inputsList, $inputName, []);
		$value = getValue($inputInfo, 'value');
		$valid = getValue($inputInfo, 'valid');
		$reason = getValue($inputInfo, 'reason');
		#Returning result
		return [
			$value,
			$reason,
			is_null($valid) ? null : ($valid ? 'valid' : 'invalid'),
			empty($reason) ? 'hidden' : ''
		];
	}
?>