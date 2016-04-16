<?php
/**
 * Migration Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * Migration Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model\Behavior
 */
class MigrationBehavior extends ModelBehavior {

/**
 * Plugin migration
 *
 * @param Model $model Model using this behavior
 * @param string $plugin Plugin key
 * @return bool True on success
 */
	public function runMigration(Model $model, $plugin = null) {
		if (! $plugin) {
			return false;
		}

		$connections = array('master');

		$plugin = Inflector::camelize($plugin);

		foreach ($connections as $connection) {
			CakeLog::info(sprintf('[migration] Start migrating %s for %s connection', $plugin, $connection));

			$messages = array();
			$ret = null;
			exec(sprintf(
				'cd %s && app/Console/cake Migrations.migration run all -p %s -c %s -i %s',
				ROOT, $plugin, $connection, $connection
			), $messages, $ret);

			// Write logs
			if (Configure::read('debug')) {
				foreach ($messages as $message) {
					CakeLog::info(sprintf('[migration]   %s', $message));
				}
			}

			CakeLog::info(
				sprintf('[migration] Successfully migrated %s for %s connection', $plugin, $connection)
			);
		}

		return true;
	}

}
