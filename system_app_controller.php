<?php

class SystemAppController extends AppController {
	
	function beforeFilter() {
		parent::beforeFilter();
		
		//	Require authorization
		if (!empty($this->Auth)) {
			$this->Auth->deny($this->action);
		}
	}
	
}