<?php
namespace Bantingan;

/*		
    This is the application starter

    Bantingan Framework v0.3
    Copyright (C) 2020 by Susilo Nurcahyo
    susilonurcahyo@gmail.com

    Some library are copyright to their respective owners.

	Bantingan Framework is free, open source, and GPL friendly. You can use it for commercial projects, open source projects, or really almost whatever you want.	

	This application is provided to you “as is” without warranty of any kind, either express or implied, including, but not limited to, the implied warranties of merchantability, fitness for a particular purpose or non-infringement.

	Want to thank me?
	- send me email, let me know how you using it,
	- want to thank more? consider buying me a coffe,
	- still not enough? let's discuss
	Thank you for your support!
*/

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

// ENV reader helper
function envVariableMapping($oldvalue, $newvalue) {
	if (is_array($oldvalue)) {
		foreach ($oldvalue as $key => $value) {
			$oldvalue[$key] = envVariableMapping($value, $newvalue[$key]??$value);
		}	
	} else {
		$oldvalue = $newvalue;
	}
	
	return $oldvalue;
}

function configFromEnv($key, $config) {		
	$newconfig = getenv("BANTINGAN3_".strtoupper($key));
	if ($newconfig != false) {
		$newconfig = json_decode($newconfig, true);
		foreach ($newconfig as $key => $newvalue) {
			$config[$key] = envVariableMapping($config[$key], $newvalue??$config[$key]);
		}				
	} 
	
	return $config;
}

// load configuration
try {
	$webconfigfile = @file_get_contents(__DIR__ .'/../config/web.config.yml');
    if ($webconfigfile === FALSE) {
		exit("Configuration File Not Found");
	} else {
		$webconfig = Yaml::parse($webconfigfile);
	}

	if (!isset($webconfig)) {
		exit("Configuration File Error");
	}

	foreach ($webconfig as $key => $settings) {
		if ($key == 'load_settings' ) {
			foreach ($settings as $settingsname => $settingsfile) {
				$settingscontent = @file_get_contents(__DIR__ .'/../config/'.$settingsfile);	
				if ($settingscontent === FALSE) {
					exit("Additional Configuration File Not Found: ".$settingsfile);
				}
				$settingsvalue = null;
				$settingsvalue = Yaml::parse($settingscontent);
				// read from environment variables
				$settingsvalue = configFromEnv($settingsname, $settingsvalue);
				// or read from files				
				define(strtoupper($settingsname), $settingsvalue); 
			}	
		} else {			
			// read from environment variables
			$settings = configFromEnv($key, $settings);			
			define(strtoupper($key), $settings);
		}
	}		
} catch (ParseException $exception) {    
	exit('Unable to parse the config file: '.$exception->getMessage());
}

// load default language
$defaultlanguage = APPLICATION_SETTINGS["Language"];
if (isset($_GET["l"])) {	
	$defaultlanguage = $_GET["l"];	// override language from querystring l
}
if(file_exists('config/language/'.$defaultlanguage.'.php')) {	 
	// load language file	
	require 'config/language/'.$defaultlanguage.'.php';
} else {
	// back to default
	require 'config/language/'.APPLICATION_SETTINGS["Language"].'.php';
}

// start the application
$application = new Applications(); 