<?php
class SettingsController extends SystemAppController {

	var $name = 'Settings';

	var $uses = array('System.SystemSetting');
	
	var $components = array(
		'Shared.History',
		'Shared.Messages',
	);

	var $helpers = array(
		'Shared.DataRenderer',
	);

	/**
	 * @var SystemSetting
	 */
	var $SystemSetting;

	var $paginate = array(
		'limit'	=> 30,
		//'sort'	=>array('SystemSetting.id'),
	);


/*===========*/
/* CALLBACKS */
/*===========*/

	function beforeFilter() {
		parent::beforeFilter();
		
		
	}



/*=======*/
/* ADMIN */
/*=======*/

	function admin_add() {
		if (!empty($this->data)) {
			if ($this->SystemSetting->addSettingByAdmin($this->data)) {
				$id = $this->SystemSetting->getInsertID();
				$this->redirect(array('action'=>'edit', $id));				
			}
			else {
				$this->Messages->add('The setting could not be saved. Please, try again.');
			}
		}

		$this->render('admin_edit_setting');
	}
	

	function admin_index() {
		$this->History->push();
		
		$options = array(			
			'order'=>array('SystemSetting.group'=>'asc'),
		);
		$SystemSettings = $this->SystemSetting->find('all', $options);
		$this->set('SystemSettings', $SystemSettings);
	}

	function admin_view($id = null) {
		
		if (!$id) {
			$this->Messages->add('Invalid setting');
			$this->redirect($this->referer());
		}
		
		$this->set('SystemSetting', $this->SystemSetting->read(null, $id));
	}

	function admin_edit($id = null) {

		if (empty($id)) {
			$this->Messages->add('Select a setting to edit', 'info');
			return $this->redirect(array('action'=>'index'));
		}

		if (!empty($this->data)) {
			if ($this->SystemSetting->updateByAdmin($id, $this->data)) {
				$this->Messages->add('The setting has been saved', 'success');
				return $this->History->back();
			}
			else {
				$this->Messages->add('The setting could not be saved. Please, try again.');
			}
		}

		$this->data = Set::merge(
			$this->SystemSetting->viewByAdmin($id),
			$this->data
		);
		
	}
	
	function admin_edit_setting($id = null) {

		if (empty($id)) {
			$this->Messages->add('Select a setting to edit', 'info');
			return $this->redirect(array('action'=>'index'));
		}

		if (!empty($this->data)) {
			if ($this->SystemSetting->updateSettingByAdmin($id, $this->data)) {
				$this->Messages->add('The setting has been saved', 'success');
				return $this->History->back();
			}
			else {
				$this->Messages->add('The setting could not be saved. Please, try again.');
			}
		}

		$this->data = Set::merge(
			$this->SystemSetting->viewByAdmin($id),
			$this->data
		);		
	}

	function admin_delete($id = null) {
		
		if (!$id) {
			$this->Messages->add('Invalid id for setting');
			return $this->History->back();
		}
		if ($this->SystemSetting->delete($id)) {
			$this->Messages->add('Setting deleted', 'success');
			return $this->History->back();
		}
		$this->Messages->add('Setting was not deleted');
		return $this->History->back();
	}
	
	function rebuild(){
		if (!file_exists(CONFIGS.'settings.config')) {
	       $this->SystemSetting->writeFile();
	    	echo "File has been built. <a href='/'>Click here to go to home page</a>"; die();
	    }
	    die('A configuration file already exists and can\'t be build. Please login and build this file from System setting section.');
	}
	
	function admin_rebuild(){
		$this->SystemSetting->writeFile();
		//$this->SystemSetting->writeConfiguration();
		$this->Messages->add('Settings have been built.', 'success');
		$this->redirect($this->referer());
	}
	
	
	
	function admin_clear_cache($confirm=null) {

		// Delete the file
		// Collect all files from multiple folders
		$folders = array('views', 'models');
		foreach($folders as $sub_folder){
			$tmp = $this->__clear_cache_files($sub_folder, array(), false);
			if (blank($file_paths)){
				$file_paths = $tmp;
			}
			$file_paths = am ($file_paths, $tmp);
		}
		
		if ($confirm==true){
			$file_paths = $this->__clear_cache_files('', $file_paths, true);
		}
		
		if ($confirm==true){
			$this->Messages->add('All cache files have been deleted.', 'success');
			$this->redirect(array('action'=>$this->action));
		}
		
		$this->set('file_paths', $file_paths);
	}
	
	
	function __clear_cache_files($type='', $file_paths=array(), $confirm=false){
		
		App::import('Core', 'Folder');
		App::import('Core', 'File');
		
		
		if (blank($file_paths)){

			// Collect all files from the given folder
			$folder = new Folder(APP . 'tmp' . DS . 'cache' . DS . $type, false);
			$file_paths = $folder->findRecursive();

			// Collect general cache files
			$folder = new Folder(APP . 'tmp' . DS . 'cache', false);
			$tmp = $folder->find();
			$cache_path = CACHE;
			foreach($tmp as $key=>$file){
				$tmp[$key] = $cache_path  . $file;
			}
			$file_paths = am ($file_paths, $tmp);
		}
		

		// Do not delete these files
		foreach ($file_paths as $key=>$file_path) {
			if (strpos($file_path, 'empty') !==false){
				unset($file_paths[$key]);
			}
			/*if (strpos($file_path, 'cake_admin_menu') !==false){
				unset($file_paths[$key]);
			}*/
		}
		
		if ($confirm){
			// Clear files
			foreach ($file_paths as $file_path) {
					$file = new File($file_path, false);
					$file->delete();
			}
		}
		
		return $file_paths;
	}
	
	
	
	
}
