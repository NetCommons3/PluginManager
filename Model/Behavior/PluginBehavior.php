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
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model 呼び出し元のモデル
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		parent::setup($model, $config);

		$this->connection = Hash::get($config, 'connection', $model->useDbConfig);

		$this->corePluginPatterns = Hash::get($config, 'corePluginPatterns', [
			'#^netcommons/#' => Plugin::PLUGIN_TYPE_CORE,
		]);
		$this->migrationPlugins = Hash::get($config, 'migrationPlugins', [
			'cakephp-plugin',
		]);
	}

/**
 * バージョンアップを実行
 *
 * @param Model $model 呼び出し元Model
 * @param string $plugin Plugin key
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function runVersionUp(Model $model, $plugin) {
		CakeLog::info(sprintf('[version up] Start version up "%s"', Hash::get($plugin, 'Plugin.name')));

		try {
			//トランザクションBegin
			$model->begin();

			if (! Hash::get($plugin, 'latest') && Hash::get($plugin, 'Plugin.id')) {
				$pluginKey = Hash::get($plugin, 'Plugin.key');
				if (! $model->uninstallPlugin($pluginKey)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				$model->deleteOldPackageDir($plugin, true);

				//img,js,cssをwebrootから削除。エラーとはしない
				$model->deleteFromWebroot($plugin);
			} else {
				if (in_array(Hash::get($plugin, 'latest.packageType'), $this->migrationPlugins, true)) {
					if (! $model->runMigration(Hash::get($plugin, 'latest.key'))) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				}

				//img,js,cssをwebrootにコピー。エラーとはしない
				$model->copyToWebroot($plugin);

				if (! $model->updateVersion(array(Hash::get($plugin, 'latest')))) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}

				$model->deleteOldPackageDir($plugin);
			}

			//キャッシュのクリア
			$model->cacheClear();

			CakeLog::info(
				sprintf('[version up] Successfully version up "%s"', Hash::get($plugin, 'Plugin.name'))
			);

			//トランザクションCommit
			$model->commit();

		} catch (Exception $ex) {
			CakeLog::info(
				sprintf('[version up] Failure version up "%s"', Hash::get($plugin, 'Plugin.name'))
			);

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

		if (is_string($data)) {
			$key = $data;
		} else {
			$key = $data[$model->Plugin->alias]['key'];
		}
		CakeLog::info(sprintf('[uninstall] Start uninstall plugin "%s"', $key));

		//トランザクションBegin
		$model->begin();

		try {
			//Pluginの削除
			if (! $model->Plugin->deleteAll(array($model->Plugin->alias . '.key' => $key), false)) {
				CakeLog::info(sprintf('[uninstall] Error(' . __LINE__ . ')'));
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoomの削除
			$conditions = array($model->PluginsRoom->alias . '.plugin_key' => $key);
			if (! $model->PluginsRoom->deleteAll($conditions, false)) {
				CakeLog::info(sprintf('[uninstall] Error(' . __LINE__ . ')'));
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoleの削除
			$conditions = array($model->PluginsRole->alias . '.plugin_key' => $key);
			if (! $model->PluginsRole->deleteAll($conditions, false)) {
				CakeLog::info(sprintf('[uninstall] Error(' . __LINE__ . ')'));
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			CakeLog::info(sprintf('[uninstall] Successfully uninstall plugin "%s"', $key));

			//キャッシュのクリア
			$model->Plugin->cacheClear();

			//トランザクションCommit
			$model->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			CakeLog::info(sprintf('[uninstall] Failure uninstall plugin "%s"', $key));

			$model->rollback($ex);
		}

		return true;
	}

/**
 * バージョンアップを実行
 *
 * @param Model $model 呼び出し元Model
 * @param string $plugin Plugin key
 * @param string|null $connection DB接続先
 * @return bool True on success
 */
	public function runMigration(Model $model, $plugin, $connection = null) {
		if (! $connection) {
			$connection = $this->connection;
		}
		return self::staticRunMigration($plugin, $connection);
	}

/**
 * Migrationの実行処理
 *
 * @param string $plugin Plugin key
 * @param string|null $connection DB接続先
 * @return bool True on success
 */
	public static function staticRunMigration($plugin, $connection = null) {
		$plugin = Inflector::camelize($plugin);

		CakeLog::info(
			sprintf('[migration] Start migrating "%s" for %s connection', $plugin, $connection)
		);

		$messages = array();
		$ret = null;
		$cmd = sprintf(
			'cd %s && Console%scake Migrations.migration run all -p %s -c %s -i %s 2>&1',
			ROOT . DS . APP_DIR, DS, escapeshellcmd($plugin), $connection, $connection
		);
		CakeLog::info('[migration]   ' . $cmd);
		exec($cmd, $messages, $ret);

		// Write logs
		foreach ($messages as $message) {
			CakeLog::info(sprintf('[migration]   %s', $message));
		}

		$result = true;
		if ($ret) {
			$matches = preg_grep('/No migrations/', $messages);
			if (count($matches) === 0) {
				CakeLog::info(
					sprintf('[migration] Failure migrated "%s" for %s connection', $plugin, $connection)
				);
				$result = false;
			} else {
				//@codeCoverageIgnoreStart
				//Migrationの戻り値が0になって処理が通らなくなったが、念のため処理として残しておく
				CakeLog::info(
					sprintf('[migration] Successfully migrated "%s" for %s connection', $plugin, $connection)
				);
				//@codeCoverageIgnoreEnd
			}
		} else {
			$matches = preg_grep('/Error: |SQLSTATE/', $messages);
			if (count($matches) === 0) {
				CakeLog::info(
					sprintf('[migration] Successfully migrated "%s" for %s connection', $plugin, $connection)
				);
			} else {
				CakeLog::info(
					sprintf('[migration] Failure migrated "%s" for %s connection', $plugin, $connection)
				);
				$result = false;
			}
		}

		return $result;
	}

/**
 * パッケージのディレクトリ削除
 *
 * @param Model $model 呼び出し元Model
 * @param string $plugin Plugin key
 * @param bool $force 強制的に実行するかどうか
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @return bool True on success
 */
	public function deleteOldPackageDir(Model $model, $plugin, $force = false) {
		if (Hash::get($plugin, 'latest.originalSource') ==
				Hash::get($plugin, 'Plugin.serialize_data.originalSource')) {
			return true;
		}

		if (Hash::get($plugin, 'latest.packageType') === 'bower') {
			$dirPath = WWW_ROOT . 'components' . DS;
			$oldPath = $dirPath . Hash::get($plugin, 'Plugin.serialize_data.originalSource');
			$newPath = $dirPath . Hash::get($plugin, 'latest.originalSource');
		} elseif (Hash::get($plugin, 'latest.packageType') === 'cakephp-plugin') {
			$oldPath = App::pluginPath(Inflector::camelize(Hash::get($plugin, 'Plugin.key')));
			$newPath = '';
		} else {
			$dirPath = VENDORS;
			$oldPath = $dirPath . strtr(Hash::get($plugin, 'Plugin.serialize_data.originalSource'), '/', DS);
			$newPath = $dirPath . strtr(Hash::get($plugin, 'latest.originalSource'), '/', DS);
		}

		if ($force || $oldPath !== $newPath && file_exists($oldPath) && file_exists($newPath)) {
			CakeLog::info(sprintf('[delete package files] Start delete package files "%s"', $oldPath));
			CakeLog::info(var_export(Hash::get($plugin, 'latest.originalSource'), true));
			CakeLog::info(var_export(Hash::get($plugin, 'Plugin.serialize_data.originalSource'), true));

			$Folder = new Folder($oldPath);
			$Folder->delete();

			CakeLog::info(
				sprintf('[delete package files] Successfully delete package files "%s"', $oldPath)
			);
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
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
	public function updateVersion(Model $model, $packages) {
		$model->loadModels([
			'Plugin' => 'PluginManager.Plugin',
		]);

		//トランザクションBegin
		$model->Plugin->begin();

		if (is_string($packages)) {
			$package = $model->Plugin->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'key' => $packages,
					'language_id' => array(Current::read('Language.id'), '0')
				),
			));
			$packages = array($package);
		}

		try {
			foreach ($packages as $package) {
				CakeLog::info(
					sprintf('[update version] Start updated version data "%s"', $package['namespace'])
				);

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
						'serialize_data' => $model->Plugin->getDataSource()->value(serialize($package), 'string'),
					);
					if (! $model->Plugin->updateAll($update, $conditions)) {
						CakeLog::info(sprintf('[update version] Line(' . __LINE__ . ') Error'));
						CakeLog::info(var_export($update, true));
						CakeLog::info(var_export($conditions, true));

						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				} else {
					$data = array(
						'language_id' => '0',
						'name' => $package['name'],
						'key' => $package['key'],
						'namespace' => $package['namespace'],
						//'type' => $type,
						'version' => $package['version'],
						'commit_version' => $package['commit_version'],
						'commited' => $package['commited'],
						'serialize_data' => serialize($package),
					);
					foreach ($this->corePluginPatterns as $pattern => $type) {
						if (preg_match($pattern, $package['namespace'])) {
							$data['type'] = Hash::get($package, 'type', $type);
							break;
						}
					}
					if (! isset($data['type'])) {
						$data['type'] = Hash::get($package, 'type');
					}
					$data['is_m17n'] = null;
					$model->Plugin->create(false);
					if (! $model->Plugin->save($data)) {
						CakeLog::info(sprintf('[update version] Line(' . __LINE__ . ') Error'));
						CakeLog::info(var_export($data, true));
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				}

				CakeLog::info(
					sprintf('[update version] Successfully updated "%s"', $package['namespace'])
				);
			}

			//トランザクションCommit
			$model->Plugin->commit();

		} catch (Exception $ex) {
			CakeLog::info(
				sprintf('[update version] Failure updated "%s"', $package['namespace'])
			);
			//トランザクションRollback
			$model->Plugin->rollback($ex);
		}
		return true;
	}

}
