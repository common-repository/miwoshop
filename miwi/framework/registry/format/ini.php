<?php
/*
* @package		Miwi Framework
* @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
* @copyright	Copyright (C) 2005-2012 Open Source Matters, Inc. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('MIWI') or die('MIWI');

class MRegistryFormatINI extends MRegistryFormat {

	protected static $cache = array ();

	public function objectToString($object, $options = array()) {
		// Initialize variables.
		$local = array ();
		$global = array ();
		
		// Iterate over the object to set the properties.
		foreach (get_object_vars($object) as $key => $value) {
			// If the value is an object then we need to put it in a local section.
			if (is_object($value)) {
				// Add the section line.
				$local[] = '';
				$local[] = '[' . $key . ']';
				
				// Add the properties for this section.
				foreach (get_object_vars($value) as $k => $v) {
					$local[] = $k . '=' . $this->getValueAsINI($v);
				}
			}
			else {
				// Not in a section so add the property to the global array.
				$global[] = $key . '=' . $this->getValueAsINI($value);
			}
		}
		
		return implode("\n", array_merge($global, $local));
	}

	public function stringToObject($data, $options = array()) {
		// Initialise options.
		if (is_array($options)) {
			$sections = (isset($options['processSections'])) ? $options['processSections'] : false;
		}
		else {
			// Backward compatibility for 1.5 usage.
			//@deprecated
			$sections = (boolean) $options;
		}
		
		// Check the memory cache for already processed strings.
		$hash = md5($data . ':' . (int) $sections);
		if (isset(self::$cache[$hash])) {
			return self::$cache[$hash];
		}
		
		// If no lines present just return the object.
		if (empty($data)) {
			return new stdClass();
		}
		
		// Initialize variables.
		$obj = new stdClass();
		$section = false;
		$lines = explode("\n", $data);
		
		// Process the lines.
		foreach ($lines as $line) {
			// Trim any unnecessary whitespace.
			$line = trim($line);
			
			// Ignore empty lines and comments.
			if (empty($line) || ($line{0} == ';')) {
				continue;
			}
			
			if ($sections) {
				$length = strlen($line);
				
				// If we are processing sections and the line is a section add the object and continue.
				if (($line[0] == '[') && ($line[$length - 1] == ']')) {
					$section = substr($line, 1, $length - 2);
					$obj->$section = new stdClass();
					continue;
				}
			}
			elseif ($line{0} == '[') {
				continue;
			}
			
			// Check that an equal sign exists and is not the first character of the line.
			if (! strpos($line, '=')) {
				// Maybe throw exception?
				continue;
			}
			
			// Get the key and value for the line.
			list ($key, $value) = explode('=', $line, 2);
			
			// Validate the key.
			if (preg_match('/[^A-Z0-9_]/i', $key)) {
				// Maybe throw exception?
				continue;
			}
			
			// If the value is quoted then we assume it is a string.
			$length = strlen($value);
			if ($length && ($value[0] == '"') && ($value[$length - 1] == '"')) {
				// Strip the quotes and Convert the new line characters.
				$value = stripcslashes(substr($value, 1, ($length - 2)));
				$value = str_replace('\n', "\n", $value);
			}
			else {
				// If the value is not quoted, we assume it is not a string.
				

				// If the value is 'false' assume boolean false.
				if ($value == 'false') {
					$value = false;
				}
				// If the value is 'true' assume boolean true.
				elseif ($value == 'true') {
					$value = true;
				}
				// If the value is numeric than it is either a float or int.
				elseif (is_numeric($value)) {
					// If there is a period then we assume a float.
					if (strpos($value, '.') !== false) {
						$value = (float) $value;
					}
					else {
						$value = (int) $value;
					}
				}
			}
			
			// If a section is set add the key/value to the section, otherwise top level.
			if ($section) {
				$obj->$section->$key = $value;
			}
			else {
				$obj->$key = $value;
			}
		}
		
		// Cache the string to save cpu cycles -- thus the world :)
		self::$cache[$hash] = clone ($obj);
		
		return $obj;
	}

	protected function getValueAsINI($value) {
		// Initialize variables.
		$string = '';
		
		switch (gettype($value)) {
			case 'integer' :
			case 'double' :
				$string = $value;
				break;
			
			case 'boolean' :
				$string = $value ? 'true' : 'false';
				break;
			
			case 'string' :
				// Sanitize any CRLF characters..
				$string = '"' . str_replace(array (
																	"\r\n",
																	"\n" 
				), '\\n', $value) . '"';
				break;
		}
		
		return $string;
	}
}
