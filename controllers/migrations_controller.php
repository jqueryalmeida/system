<?php

class MigrationsController extends SystemAppController {

	var $name = 'Migrations';
	var $uses = array();
	var $components = array(
		'MigrationManager'
	);
	
	/*
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

	function _update_01_add_event_name_and_location_to_event_registration($db) {
		
		$result = $db->execute("ALTER TABLE `event_registrations` ADD `event_name` VARCHAR(255) NULL AFTER `email`");
		if (!$result) return false;
		
		$result = $db->execute("ALTER TABLE `event_registrations` ADD `event_location` VARCHAR(255) NULL AFTER `event_name`");
		if (!$result) return false;
	}

	function _update_02_remove_custom_field_unique_acc_no_initial($db) {
		
		$customFieldId = 61;
		$result = $db->execute("DELETE FROM `eblast_custom_fields` WHERE `id` = {$customFieldId}");
		if (!$result) return false;
		
		$result = $db->execute("DELETE FROM `eblast_subscriber_fields` WHERE `eblast_custom_field_id` = {$customFieldId}");
		if (!$result) return false;
	}
	
	function _update_03_custom_fields_options($db) {
		$EblastCustomField = ClassRegistry::init('EblastCustomField', 'model');
		
		$result = true;
		if (!$EblastCustomField->hasField('options')){
			$result = $db->execute("ALTER TABLE `eblast_custom_fields` ADD `options` TEXT NULL;");
		}
		if (!$result) return false;
	}
}
