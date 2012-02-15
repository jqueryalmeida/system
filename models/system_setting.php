<?php
class SystemSetting extends AppModel {
	var $name = 'SystemSetting';
	
	var $useTable = 'system_settings';
	var $displayField = 'title';
	var $recursive = -1;
	var $transactional = true;

	var $actsAs = array(
		'Shared.Multivalidatable',
	);
	
	
	
	var $validate = array(
		
	);
	
	
	var $validationSets = array(
		'add_or_update_setting'=>array(
				'group'=>array(
					array(
						'rule'			=>	array('notEmpty'),
						'allowEmpty'	=> 	false,
						'required'		=> true,
						'message'		=> 	'Group cannot be blank',
					),	
					'No Spaces'=>array(
						'rule'			=>	array('validateNoSpaces'),
						'allowEmpty'	=> 	false,
						'required'		=> true,
						'message'		=> 	'Spaces not allowed',
					),					
				),
				'key'=>array(
					array(
						'rule'			=>	array('notEmpty'),
						'allowEmpty'	=> 	false,
						'required'		=> true,
						'message'		=> 	'Key cannot be blank',
					),	
					'No Spaces'=>array(
						'rule'			=>	array('validateNoSpaces'),
						'allowEmpty'	=> 	false,
						'required'		=> true,
						'message'		=> 	'Spaces not allowed',
					),				
				),
		),
	);

	
/*==============*/
/* ASSOCIATIONS */
/*==============*/


/*===========*/
/* CALLBACKS */
/*===========*/
	
	
	function validateNoSpaces($value){
		$array_keys = array_keys($value);
		$val = $value[array_pop($array_keys)];
		
		if (preg_match('#[\s]#', $val)){
			return false;
		}
		return true;
	}

	function beforeValidate($options=array()) {
	
		return true;
	}
	
	function beforeSave($options=array()) {
		
		return true;
	}
	
	function beforeDelete($cascade) {
		
		return true;
	}
	
	function afterFind($results) {
		
		return $results;
	}
	
	
	
	public function afterSave($created) {
		$this->serialize();
        
        $this->writeFile();
    }

    public function afterDelete() {
    	
        $this->writeFile();
    }

/*======*/
/* CRUD */
/*======*/
    
    function addSettingByAdmin($data) {

    	$this->restoreDefaultValidation();
    	$this->setValidation('add_or_update_setting');
    	$this->create();
		return $this->saveAll($data, array('validate'=>'first'));
	}
	function updateSettingByAdmin($id, $data) {
		$this->validationErrors = array();
		
		$this->restoreDefaultValidation();
		$this->setValidation('add_or_update_setting');
		
		$data['SystemSetting']['id'] = $id;
		return $this->saveAll($data, array('validate'=>'first'));
	}

	
	
	function addByAdmin($data) {
        $this->create();
		return $this->saveAll($data, array('validate'=>'first'));
	}
	
	function updateByAdmin($id, $data) {
		$this->validationErrors = array();
		
		$data['SystemSetting']['id'] = $id;
		return $this->saveAll($data, array('validate'=>'first'));
	}
	
	function viewByAdmin($id) {
		$options = array(
			'conditions' => array('SystemSetting.id' => $id),
			'contain' => false,
		); 
		return $this->find('first', $options);
	}
	
	
	
	
	
	/**
	 * All key/value pairs are made accessible from Configure class
	 *
	 * @return void
	 */
    public function writeConfiguration() {
        $settings = $this->find('all', array(
            'fields' => array(
        		'SystemSetting.group',
                'SystemSetting.key',
                'SystemSetting.value',
            ),
            /*'cache' => array(
                'name' => 'setting_write_configuration',
                'config' => 'setting_write_configuration',
            ),*/
        ));
        foreach($settings AS $setting) {
            Configure::write($setting['SystemSetting']['key'], $setting['SystemSetting']['value']);
        }
    }
	/**
	 * Find list and save the file 
	 * Data required in bootstrap.
	 *
	 * @return void
	 */
    public function writeFile() {
	    
        $settings = $this->find('all', array(
            'fields' => array(
        		'group',
                'key',
                'value',
            ),
            'order' => array(
                'SystemSetting.group' => 'ASC',
            	'SystemSetting.key' => 'ASC',
            ),
        ));
        
        $list = array();
        foreach($settings as $setting){
        	$full_key	= $setting['SystemSetting']['group'] . '.' . $setting['SystemSetting']['key'];;
        	$list[$full_key] = $setting['SystemSetting']['value'];
        }
        
        // Added timestamp
        $list['modified'] = time();
        $serialized = serialize($list);
        
        $filePath = APP.'config'.DS.'settings.config';
        App::import('Core', 'File');
        $file = new File($filePath, true);
        $file->write($serialized);
        
        // Update configuration
        foreach($list as $key=>$val){
        	Configure::write($key, $val);
        }
        
    }



/*=========*/
/* YAML CONFIGURATION */
/*=========*/


	/**
	 * All key/value pairs are made accessible from Configure class
	 *
	 * @return void
	 */
   /* public function writeConfiguration() {
        $settings = $this->find('all', array(
            'fields' => array(
                'SystemSetting.key',
                'SystemSetting.value',
            ),
            'cache' => array(
                'name' => 'setting_write_configuration',
                'config' => 'setting_write_configuration',
            ),
        ));
        foreach($settings AS $setting) {
            Configure::write($setting['SystemSetting']['key'], $setting['SystemSetting']['value']);
        }
    }*/
	/**
	 * Find list and save yaml dump in app/config/settings.yml file.
	 * Data required in bootstrap.
	 *
	 * @return void
	 */
    /*public function updateYaml() {
        $list = $this->find('list', array(
            'fields' => array(
                'key',
                'value',
            ),
            'order' => array(
                'SystemSetting.key' => 'ASC',
            ),
        ));
        $filePath = APP.'config'.DS.'settings.yml';
        App::import('Core', 'File');
        $file = new File($filePath, true);
        $listYaml = Spyc::YAMLDump($list, 4, 60);
        $file->write($listYaml);
    }*/
	
	
}
