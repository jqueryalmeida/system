<?php

App::import('ConnectionManager');
class MigrationManagerComponent extends Object {
	
	var $name = 'MigrationManager';
	
	var $Controller = null;
	var $settings = array();
	
	/**
	 * Component initialize callback 
	 * 
	 * @param $Controller AppController
	 * @param $settings array
	 * @return void
	 */
	function initialize(&$Controller, $settings = array()) {
		$this->Controller =& $Controller;
		$this->settings = Set::merge(
			array(
				'schemaInfoTable' => 'system_schema_info',
				'clearCache' => true,
				'timeLimit' => 10 * MINUTE,
				'pattern' => '#^_update_([0-9]+)#i',
				'dbConfig' => 'default',
			),
			$settings
		);
	}
	
	function reset() {
		
	}
	
	/**
	 * Fetches the data source
	 * 
	 * @param $db mixed Accepts string, DboSource, or null
	 * @return DboSource
	 */
	function getDataSource(&$db=null) {
		switch (true) {
			case is_object($db):
				break;
			
			case is_string($db):
				$db = ConnectionManager::getDataSource($db);
				break;
				
			default:
				$db = ConnectionManager::getDataSource($this->settings['dbConfig']);
		}
		
		$db->cacheSources = false;
		return $db;
	}
	
	/**
	 * Checks for existence of schema info table.
	 * 
	 * @param $db
	 * @return bool
	 */
	function hasSchemaInfo(&$db=null) {
		$db =& $this->getDataSource($db);
		return in_array($this->settings['schemaInfoTable'], $db->listSources(), true);
	}
	
	/**
	 * Creates the schema info table.
	 * 
	 * @param $db
	 * @return bool
	 */
	function createSchemaInfo(&$db=null) {
		$db =& $this->getDataSource($db);
		
		$sql = <<<sql
			CREATE TABLE IF NOT EXISTS `{$this->settings['schemaInfoTable']}` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`version` int(11) NOT NULL,
				PRIMARY KEY (`id`)
			);
sql;
		$db->execute($sql);
		
		$sql = <<<sql
			INSERT INTO `{$this->settings['schemaInfoTable']}` VALUES (1, 0);
sql;
		return $db->execute($sql);
	}
		
	/**
	 * Determines schema version in database.
	 * 
	 * @param $db Data source object or config key name
	 * @param $autoCreate=true Creates schema info if missing
	 * @return int
	 */
	function getDbVersion(&$db=null, $autoCreate=true) {
		$db =& $this->getDataSource($db);
		
		if ($this->hasSchemaInfo($db)) {
			$query = array(
				'table' => $this->settings['schemaInfoTable'],
				'alias' => 'SchemaInfo',
				'fields' => array('version'),
				'conditions' => array('id' => 1),
				'limit' => 1,
				'order' => null,
				'group' => null,
			);
			$NoModel = '';
			$sql = $db->buildStatement($query, $NoModel);
			$result = $db->fetchRow($sql);
			
			if (!empty($result['SchemaInfo']['version'])) {
				return intval($result['SchemaInfo']['version']);
			}
			else {
				return 0;
			}
		}
		else {
			if ($autoCreate) {
				$this->createSchemaInfo($db);
				return $this->getDbVersion($db, false);
			}
			else {
				return 0;
			}
		}
	}
	
	/**
	 * Fetches all migrations (match the pattern) and sorts them
	 * in order of incrementing version.
	 * 
	 * @param $db
	 * @param $fromVersion int
	 * @param $toVersion int
	 * @return array migrations
	 */
	function getMigrations() {
		
		$methods = get_class_methods($this->Controller);
		sort($methods, SORT_STRING);
		
		$migrationMethods = array();
		$versions = array();
		foreach ($methods as $method) {
			if (preg_match($this->settings['pattern'], $method, $matches)) {
				$version = intval(preg_replace('#^0+#', '', $matches[1]));
				
				$migrationMethods[] = array(
					'method' => $method,
					'version' => $version,
				);
				$versions[] = $version;
			}
		}
		array_multisort($versions, SORT_NUMERIC, $migrationMethods);
		
		return $migrationMethods;
	}
	
	/**
	 * Fetches migrations required for database to reach a version.
	 * 
	 * @param $db
	 * @param $fromVersion int
	 * @param $toVersion int
	 * @return array migrations
	 */
	function getRequiredMigrations($db=null, $fromVersion=null, $toVersion=null) {
		$db =& $this->getDataSource($db);
		
		if (is_null($fromVersion)) {
			$fromVersion = $this->getDbVersion($db);
		}
		
		if (is_null($toVersion)) {
			$toVersion = $this->getMigrationsVersion();
		}
		
		$migrations = $this->getMigrations();
		
		
		if (empty($migrations)) {
			return array();
		}
		else {
			$required = array();
			foreach ($migrations as $migration) {
				if (($migration['version'] > $fromVersion) && ($migration['version'] <= $toVersion)) {
					$required[] = $migration;
				}
			}
			
			return $required;
		}
	}
	
	/**
	 * Determines schema version from migration scripts.
	 * 
	 * @return int
	 */
	function getMigrationsVersion() {
		
		$migrations = $this->getMigrations();
		
		if (empty($migrations)) {
			return 0;
		}
		else {
			$last = end($migrations);
			return $last['version'];
		}
	}
	
	/**
	 * Runs missing migrations
	 * 
	 * @param $db
	 * @return unknown_type
	 */
	function run($db=null) {
		set_time_limit($this->settings['timeLimit']);
		$this->clearModelCache();
		
		$dryRun = isset($this->Controller->passedArgs['dryRun']) ? !empty($this->Controller->passedArgs['dryRun']) : true;
		$executeUrl = Router::url(array('dryRun'=>0));
		
		$db =& $this->getDataSource($db);
		$this->out("Active database: " . $db->configKeyName);
		
		$dbVersion = $this->getDbVersion($db);
		$this->out("Database version: " . $dbVersion);
		
		$migrationsVersion = $this->getMigrationsVersion();
		$this->out("Migrations version: " . $migrationsVersion);
		
		$migrations = $this->getRequiredMigrations($db, $dbVersion, $migrationsVersion);
		
		if (empty($migrations)) {
			$this->out('Everything is up-to-date');
		}
		else {
			
			if ($dryRun) {
				$this->out("<a href='{$executeUrl}'>Execute the following migrations:</a>");
				foreach ($migrations as $migration) {
					$this->out("=> {$migration['version']}: {$migration['method']}()");
				}
			}
			else {
				foreach ($migrations as $migration) {
					$success = $this->executeMigration($db, $migration);
					
					if ($success) {
						$dbVersion = $migration['version'];
						$this->setVersion($db, $dbVersion);
					}
					else {
						$this->out("Migration halted");
						break;
					}
				}
				
				if ($success) {
					$this->out("Migration successful, database at version: {$migration['version']}");
				}
				else {
					$this->out("Migration FAILED, database at version: {$migration['version']}");
				}
			}
		}
	}
	
	function executeMigration($db, $migration) {
		if ($this->Controller->{$migration['method']}($db) !== false) {
			$this->clearModelCache();
			$this->out("==> {$migration['version']}: {$migration['method']}() -- Done");
			return true;
		}
		else {
			$this->out("Error during {$migration['method']}()");
			return false;
		}
	}
	
	function clearModelCache() {
		App::import('Folder');
		$folder = new Folder(CACHE . 'models', true, 0755);
		$filePaths = $folder->findRecursive();
		$excludes = array('.', '..', 'empty');
		foreach ($filePaths as $filePath) {
			$fileName = basename($filePath);
			if (!is_file($fileName) || in_array($fileName, $excludes, true)) {
				continue;
			}
			else {
				@unlink($filePath);
			}
		}
	}
	
	function setVersion($db, $version) {
		$sql = <<<sql
			UPDATE `{$this->settings['schemaInfoTable']}` SET `version` = {$version} WHERE `id` = 1;
sql;
		return $db->execute($sql);
	}
	
	function out($msg) {
		echo "<pre>{$msg}</pre>";
	}
	
}