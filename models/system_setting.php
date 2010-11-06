<?php
class SystemSetting extends AppModel {
	var $name = 'SystemSetting';
	
	var $useTable = 'system_settings';
	var $displayField = 'title';
	var $recursive = -1;
	var $transactional = true;

	var $actsAs = array(
	);
	

/*==============*/
/* ASSOCIATIONS */
/*==============*/


/*===========*/
/* CALLBACKS */
/*===========*/

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
        
        $this->writeConfiguration();
    }

    public function afterDelete() {
        $this->serialize();
        $this->writeConfiguration();
    }

/*======*/
/* CRUD */
/*======*/

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
	 * Find list and save yaml dump in app/config/settings.yml file.
	 * Data required in bootstrap.
	 *
	 * @return void
	 */
    public function serialize() {
	    
        $list = $this->find('list', array(
            'fields' => array(
                'key',
                'value',
            ),
            'order' => array(
                'SystemSetting.key' => 'ASC',
            ),
        ));
        $serialized = serialize($list);
        
        $filePath = APP.'config'.DS.'settings.config';
        App::import('Core', 'File');
        $file = new File($filePath, true);
        $file->write($serialized);
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
