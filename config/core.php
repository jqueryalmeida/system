<?php

	/*
	 * Read site specific settings
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
    	if (Configure::read('debug')>=1){
    		echo '<pre>
    			/app/config/settings.config file doesn\'t exist.
    			This file can be automatically rebuilt by edit one of the settings in the backend!
    			</pre>
    			';
    	}    	
    }

   
?>