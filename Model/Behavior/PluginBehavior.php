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
App::uses('Folder', 'Utility');
App::uses('Plugin', 'PluginManager.Model');

/**
 * Plugin Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model\Behavior
 */
class PluginBehavior extends ModelBehavior {

/**
 * バージョンアップを実行
 *
 * @param Model $model 呼び出し元Model
 * @param string $plugin Plugin key
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function runVersionUp(Model $model, $plugin) {
		try {
			//トランザクションBegin
			$model->begin();

			if (! Hash::get($plugin, 'latest') && Hash::get($plugin, 'Plugin.id')) {
				if (! $model->uninstallPlugin(Hash::get($plugin, 'Plugin.key'))) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				$model->deletePackageDir($plugin);
			} else {
				if (Hash::get($plugin, 'latest.packageType') === 'cakephp-plugin') {
					if (! $model->runMigration(Hash::get($plugin, 'latest.key'))) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				}
				if (! $model->updateVersion(array(Hash::get($plugin, 'latest')))) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				if (Hash::get($plugin, 'latest.originalSource') !==
						Hash::get($plugin, 'Plugin.serialize_data.originalSource')) {
					$model->deletePackageDir($plugin);
				}
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
 * Pluginのアンインストール
 *
 * @param Model $model 呼び出し元Model
 * @param array $data Pluginデータ
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
			$key = $data[$model->Plugin->alias]['key'];
		}

		try {
			//Pluginの削除
			if (! $model->Plugin->deleteAll(array($model->Plugin->alias . '.key' => $key), false)) {
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
 * バージョンアップを実行
 *
 * @param Model $model 呼び出し元Model
 * @param string $plugin Plugin key
 * @return bool True on success
 */
	public function runMigration(Model $model, $plugin) {
		$connection = 'master';
		$plugin = Inflector::camelize($plugin);

		CakeLog::info(sprintf('[migration] Start migrating %s for %s connection', $plugin, $connection));

		$messages = array();
		$ret = null;
		exec(sprintf(
			'cd %s && app/Console/cake Migrations.migration run all -p %s -c %s -i %s',
			ROOT, $plugin, $connection, $connection
		), $messages, $ret);

		$result = true;
		if ($ret) {
			$matches = preg_grep('/No migrations/', $messages);
			if (count($matches) === 0) {
				CakeLog::info(
					sprintf('[migration] Failure migrated %s for %s connection', $plugin, $connection)
				);
				$result = false;
			} else {
				CakeLog::info(
					sprintf('[migration] Successfully migrated %s for %s connection', $plugin, $connection)
				);
			}
		} else {
			CakeLog::info(
				sprintf('[migration] Successfully migrated %s for %s connection', $plugin, $connection)
			);
		}

		// Write logs
		foreach ($messages as $message) {
			CakeLog::info(sprintf('[migration]   %s', $message));
		}

		return $result;
	}

/**
 * パッケージのディレクトリ削除
 *
 * @param Model $model 呼び出し元Model
 * @param string $plugin Plugin key
 * @return bool True on success
 */
	public function deletePackageDir(Model $model, $plugin) {
		if (Hash::get($plugin, 'Plugin.serialize_data.packageType') === 'bower') {
			$dirPath = WWW_ROOT . 'components' . DS;
			$dirPath .= Hash::get($plugin, 'Plugin.serialize_data.originalSource');
		} elseif (Hash::get($plugin, 'Plugin.serialize_data.packageType') === 'cakephp-plugin') {
			$dirPath = App::pluginPath(Inflector::camelize(Hash::get($plugin, 'Plugin.key')));
		} else {
			$dirPath = VENDORS;
			$dirPath .= strtr(Hash::get($plugin, 'Plugin.serialize_data.originalSource'), '/', DS);
		}
		if (file_exists($dirPath)) {
			$Folder = new Folder($dirPath);
			$Folder->delete();
		}

		return true;
	}

/**
 * バージョン情報をDBに更新する
 *
 * @param Model $model 呼び出し元Model
 * @param array $packages パッケージリスト
 * @return bool
 * @throws InternalErrorException
 */
	public function updateVersion(Model $model, $packages) {
		$model->loadModels([
			'Plugin' => 'PluginManager.Plugin',
		]);

		//トランザクションBegin
		$model->begin();

		try {
			foreach ($packages as $package) {
				if ($package['namespace'] === 'netcommons/photo-albums') {
					$conditions = array(
						'namespace' => array('netcommons/photo-albums', 'netcommons/photo_albums')
					);
				} else {
					$conditions = array('namespace' => $package['namespace']);
				}

				$count = $model->Plugin->find('count', array(
					'recursive' => -1,
					'conditions' => $conditions,
				));
				if ($count > 0) {
					$update = array(
						'version' => '\'' . $package['version'] . '\'',
						'commit_version' => '\'' . $package['commit_version'] . '\'',
						'commited' => '\'' . $package['commited'] . '\'',
						'serialize_data' => '\'' . serialize($package) . '\'',
					);
					if (! $model->Plugin->updateAll($update, $conditions)) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				} else {
					if (preg_match('#^netcommons/#', $package['namespace'])) {
						$type = Hash::get($package, 'type', Plugin::PLUGIN_TYPE_CORE);
					} else {
						$type = $package['type'];
					}
					$data = array(
						'language_id' => '0',
						'name' => $package['name'],
						'key' => $package['key'],
						'namespace' => $package['namespace'],
						'type' => $type,
						'version' => $package['version'],
						'commit_version' => $package['commit_version'],
						'commited' => $package['commited'],
						'serialize_data' => serialize($package),
					);
					$model->Plugin->create(false);
					if (! $model->Plugin->save($data)) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				}
			}

			//トランザクションCommit
			$model->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$model->rollback($ex);
		}
		return true;
	}

}
