<?php

	/*
	 * Read database settings
	 */
	if (file_exists(CONFIGS.'settings.config')) {
        $serialized = file_get_contents(CONFIGS.'settings.config');
        $settings = unserialize($serialized);
        if (!empty($settings)){
	        foreach ($settings AS $settingKey => $settingValue) {
	            Configure::write($settingKey, $settingValue);
	        }
        }else{
        	die('Failed to read a configuration file');
        }
    }else{
		if (!isset($_REQUEST['url']) || (isset($_REQUEST['url']) && $_REQUEST['url']!= 'system/settings/rebuild')){
			echo '<pre>';
	    	echo '/app/config/settings.config doesn\'t exist.';
	    	echo " Click <a href='/system/settings/rebuild'>this link</a> to rebuild a configuration file.";
			echo '</pre>';
		
			die();	
		}	
    }

   
?>