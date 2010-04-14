<?php

class MigrationsController extends SystemAppController {

	var $name = 'Migrations';
	var $uses = array();
	var $components = array(
		'MigrationManager'
	);
	
	/**
	 * @var MigrationManagerComponent
	 */
	var $MigrationManager;

	function beforeFilter() {
		parent::beforeFilter();
		
		$this->autoRender = false;
		$this->autoLayout = false;
	}
	
	function run() {
		$this->MigrationManager->run();
	}
	
	
}
