<?php

class SystemAppController extends AppController {
	
	function beforeFilter() {
		parent::beforeFilter();				
	}

	
	
	function getAdminMenu() {
		return array(
			'Settings' => array(
				'url' => array('plugin'=>'system', 'controller'=>'settings', 'action'=>'index', 'prefix'=>'admin'),
				'permission' => 'Users',
				'children' => array(
					'All settings' => array(
						'url' => array('plugin'=>'system', 'controller'=>'settings', 'action'=>'index', 'prefix'=>'admin'),
					),
					'New setting' => array(
						'url' => array('plugin'=>'system', 'controller'=>'settings', 'action'=>'add', 'prefix'=>'admin'),
					),
					'Edit setting' => array(
						'url' => array('plugin'=>'system', 'controller'=>'settings', 'action'=>'edit', 'prefix'=>'admin'),
					),
					
					'Rebuild Configuration' => array(
						'url' => array('action'=>'rebuild', 'controller'=>'settings', 'admin'=>true, 'prefix'=>'admin'),
					),
				),				
			),	
			'Caching' => array(
				'url' => array('plugin'=>'system', 'controller'=>'settings', 'action'=>'clear_cache', 'prefix'=>'admin'),
				'permission' => 'Users',
				'children' => array(
					'Cache files' => array(
						'url' => array('plugin'=>'system', 'controller'=>'settings', 'action'=>'clear_cache', 'prefix'=>'admin'),
					),
					
				),				
			),		
		);
	}
	
}