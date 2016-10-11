<?php
/**
 * Plugin Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');
App::uses('File', 'Utility');

/**
 * Plugin Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model\Behavior
 */
class PluginBehavior extends ModelBehavior {

/**
 * Uninstall plugin
 *
 * @param Model $model Model using this behavior
 * @param array $data Plugin data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function uninstallPlugin(Model $model, $data) {
		$model->loadModels([
			'Plugin' => 'PluginManager.Plugin',
			'PluginsRole' => 'PluginManager.PluginsRole',
			'PluginsRoom' => 'PluginManager.PluginsRoom',
		]);

		//トランザクションBegin
		$model->begin();

		if (is_string($data)) {
			$key = $data;
		} else {
			$key = $data[$model->alias]['key'];
		}

		try {
			//Pluginの削除
			if (! $model->deleteAll(array($model->alias . '.key' => $key), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoomの削除
			$conditions = array($model->PluginsRoom->alias . '.plugin_key' => $key);
			if (! $model->PluginsRoom->deleteAll($conditions, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoleの削除
			$conditions = array($model->PluginsRole->alias . '.plugin_key' => $key);
			if (! $model->PluginsRole->deleteAll($conditions, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$model->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$model->rollback($ex);
		}

		return true;
	}

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

/**
 * Get plugin information data from composer.lock
 *
 * @param Model $model Model using this behavior
 * @param string $namespace Plugin namespace
 * @return mixed array|bool
 */
	public function getComposer(Model $model, $namespace = null) {
		static $composers = null;

		if (! $composers) {
			$filePath = ROOT . DS . 'composer.lock';
			$file = new File($filePath);
			$contents = $file->read();
			$file->close();

			$composers = json_decode($contents, true);
		}
		if (! $namespace) {
			return $composers;
		}

		$ret = Hash::extract($composers['packages'], '{n}[name=' . $namespace . ']');
		if ($ret) {
			return $ret[0];
		}
		$ret = Hash::extract($composers['packages-dev'], '{n}[name=' . $namespace . ']');
		if ($ret) {
			return $ret[0];
		}
		return null;
	}

/**
 * Get external plugin
 *
 * @param Model $model Model using this behavior
 * @param array $pluginKey Plugin key
 * @return array Plugins data
 * @throws InternalErrorException
 */
	public function getExternalPlugins(Model $model, $pluginKey = null) {
		static $plugins = null;

		if (! $plugins) {
			$composers = $model->getComposer();
			$index = 0;
			$packageKeys = array('packages');
			foreach ($packageKeys as $key) {
				foreach ($composers[$key] as $package) {
					if (preg_match('#^netcommons/#', $package['name'])) {
						continue;
					}

					$plugins[$index]['Plugin']['key'] = Security::hash($package['name'], 'md5');
					$plugins[$index]['Plugin']['namespace'] = $package['name'];
					if (isset($package['extra']['installer-name'])) {
						$plugins[$index]['Plugin']['name'] = $package['extra']['installer-name'];
					} else {
						$plugins[$index]['Plugin']['name'] = $package['name'];
					}

					$plugins[$index]['composer'] = $package;
					$index++;
				}
			}

			$plugins = Hash::sort($plugins, '{n}.Plugin.name');
		}

		if (! $pluginKey) {
			return $plugins;
		}

		$plugin = Hash::extract($plugins, '{n}.Plugin[key=' . $pluginKey . ']');
		$ret[0]['Plugin'] = $plugin[0];
		$composer = Hash::extract($plugins, '{n}.composer[name=' . $ret[0]['Plugin']['namespace'] . ']');
		$ret[0]['composer'] = $composer[0];

		return $ret;
	}

}
