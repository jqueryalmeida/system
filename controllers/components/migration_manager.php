<?php

/**
 * MigrationManagerComponent
 * =========================
 * 
 * Manages and executes database migration updates.
 * 
 * @author	alvin savoy
 * @version	0.1
 * 
 * 
 * Usage:
 * ======
 * 
 * 1. Add this component to a controller.
 * 
 * 2. Create an action that will execute $this->MigrationManager->run();
 *    This action is where you will execute migrations. Keep it secure.
 * 
 * 3. Each time you need to make a change to the database, create an action
 *    in the controller, example:
 *    
 *    function _update_01_add_user_id_field($db) {
 *    		$result = $db->execute("ALTER TABLE `event` ADD `user_id` INT(11) NULL AFTER `title`");
			if (!$result) return false;
 *    }
 *    
 * 4. Rules for migration functions:
 *    Must fit the pattern _update_##_a_user_friendly_description()
 *    Each migration function must contain a unique, incremental version number.
 *    It is provided a database connection, but you can also use $this->loadModel()
 *    and use model methods.
 *    You must return false when an error occurs; this stops execution of migrations and leaves
 *    the database version at the last successfull migration.
 * 
 * 5. This component will execute only the migrations necessary to keep the database up-to-date
 *    with the migrations.
 *    
 * 6. There is currently no support for "down-grading".
 * 
 * 
 * Configuration:
 * ==============
 * 
 * schemaInfoTable		table to store database version ["system_schema_info"]
 * pattern				regex to identify migration methods, and to retrieve version number ["#^_update_([0-9]+)#i"]
 * dbConfig				database config to use ["default"]
 * 
 */

App::import('ConnectionManager');
class MigrationManagerComponent extends Object {
	
	/**
	 * Name of this component
	 * @var string
	 */
	var $name = 'MigrationManager';
	
	/**
	 * Reference to Controller
	 * @var AppController
	 */
	var $Controller = null;
	
	/**
	 * Settings
	 * @var array
	 */
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
				'timeLimit' => 10 * MINUTE,
				'pattern' => '#^_update_([0-9]+)#i',
				'dbConfig' => 'default',
			),
			$settings
		);
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
			$result = $db->fetchRow($db->buildStatement($query, null));
			
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
	
	/**
	 * Executes a migration action
	 * 
	 * @param $db
	 * @param $migration
	 * @return bool success
	 */
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
	
	/**
	 * Clears CakePHP's model cache
	 * 
	 */
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
	
	/**
	 * Sets version in database schema info table.
	 * 
	 * @param $db
	 * @param $version
	 * @return bool success
	 */
	function setVersion($db, $version) {
		$sql = <<<sql
			UPDATE `{$this->settings['schemaInfoTable']}` SET `version` = {$version} WHERE `id` = 1;
sql;
		return $db->execute($sql);
	}
	
	/**
	 * Outputs a message
	 * 
	 * @param $msg
	 */
	function out($msg) {
		echo "<pre>{$msg}</pre>";
	}
	
}