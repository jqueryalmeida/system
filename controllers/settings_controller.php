<?php
class SettingsController extends AppController {

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


	function admin_index() {
		$this->History->push();
		
		$options = array(
			'order'=>array('SystemSetting.id'),
		);
		$this->paginate = am($this->paginate, $options);
		
		$SystemSettings = $this->paginate('SystemSetting');
		$this->set('SystemSettings', $SystemSettings);
	}

	function admin_view($id = null) {
		
		if (!$id) {
			$this->Messages->add('Invalid setting');
			$this->redirect($this->referer());
		}
		
		$this->set('SystemSetting', $this->SystemSetting->read(null, $id));
	}

	function admin_add() {
		
		if (!empty($this->data)) {
			if ($this->SystemSetting->addByAdmin($this->data)) {
				$this->Messages->add('The shared setting has been saved', 'success');
				return $this->History->back();
			}
			else {
				$this->Messages->add('The shared setting could not be saved. Please, try again.');
			}
		}
		
		$this->render('admin_edit');
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
	
	
	function admin_clear_cache($confirm='') {
		App::import('Core', 'Folder');
		App::import('Core', 'File');

		$folder = new Folder(APP . 'tmp' . DS . 'cache' . DS . env('HTTP_HOST'), true);
		$file_paths = $folder->findRecursive();

		//pr('CLEARING CACHE');

		foreach ($file_paths as $file_path) {
			$file = new File($file_path, false);
			
			if ($confirm==true){
				$file->delete();
			}
		}
		
		if ($confirm==true){
			$this->redirect(array('action'=>$this->action));
		}
		
		$this->set('file_paths', $file_paths);
	}
 	
}
