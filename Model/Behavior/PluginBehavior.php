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
 * Install plugin data.
 *
 * @param Model $model Model using this behavior
 * @param array $data Plugin data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function installPlugin(Model $model, $data) {
		$model->loadModels([
			'Plugin' => 'PluginManager.Plugin',
			'PluginsRole' => 'PluginManager.PluginsRole',
			'PluginsRoom' => 'PluginManager.PluginsRoom',
			'Language' => 'M17n.Language',
		]);

		//トランザクションBegin
		$model->setDataSource('master');
		$dataSource = $model->getDataSource();
		$dataSource->begin();

		//言語データ取得
		$languages = $model->Language->find('list', array(
			'fields' => array('Language.code', 'Language.id')
		));

		try {
			//Pluginテーブルの登録
			$currentLang = Configure::read('Config.language');

			foreach (Configure::read('Config.languageEnabled') as $lang) {
				$conditions = array(
					'Plugin.language_id' => $languages[$lang],
					'Plugin.key' => $data['Plugin']['key'],
				);

				if (! $plugin = $model->find('first', array(
					'recursive' => -1,
					'conditions' => $conditions,
				))) {
					$plugin = $model->create(array('id' => null));
					if (! isset($data['Plugin']['type'])) {
						$data['Plugin']['type'] = self::PLUGIN_TYPE_CORE;
					}
					if (! isset($data['Plugin']['weight'])) {
						$data['Plugin']['weight'] = $model->getMaxWeight($data['Plugin']['type']) + 1;
					}
				}

				Configure::write('Config.language', $lang);

				$plugin['Plugin'] = Hash::merge($plugin['Plugin'], $data['Plugin'], array(
					'language_id' => $languages[$lang],
					'name' => __d($data['Plugin']['key'], $data['Plugin']['name'])
				));

				$model->save($plugin, false);
			}

			Configure::write('Config.language', $currentLang);

			//PluginsRoleテーブルの登録
			if (isset($data['PluginsRole'])) {
				$model->PluginsRole->savePluginRoles($data);
			}

			//PluginsRoomテーブルの登録
			if (isset($data['PluginsRoom'])) {
				$model->PluginsRoom->savePluginRooms($data);
			}

			//トランザクションCommit
			$dataSource->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$dataSource->rollback();
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
	}

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
		$model->setDataSource('master');
		$dataSource = $model->getDataSource();
		$dataSource->begin();

		try {
			//Pluginの削除
			if (! $model->deleteAll(array($model->alias . '.key' => $data[$model->alias]['key']), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoomの削除
			if (! $model->PluginsRoom->deleteAll(array($model->PluginsRoom->alias . '.plugin_key' => $data[$model->alias]['key']), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoleの削除
			if (! $model->PluginsRole->deleteAll(array($model->PluginsRole->alias . '.plugin_key' => $data[$model->alias]['key']), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$dataSource->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$dataSource->rollback();
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
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
			$packageKeys = array('packages', 'packages-dev');
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
