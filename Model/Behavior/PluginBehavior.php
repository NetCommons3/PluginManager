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
App::uses('Plugin', 'PluginManager.Model');

/**
 * Plugin Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model\Behavior
 */
class PluginBehavior extends ModelBehavior {

/**
 * Composersのファイル取得
 *
 * @var array
 */
	public $composers = array();

/**
 * Bowersのファイル取得
 *
 * @var array
 */
	public $bowers = array();

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
 * Migrationの実行
 *
 * @param Model $model 呼び出し元Model
 * @param string $plugin Plugin key
 * @return bool True on success
 */
	public function runMigration(Model $model, $plugin = null) {
		if (! $plugin) {
			return false;
		}

		$connection = 'master';

		$plugin = Inflector::camelize($plugin);

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

		return true;
	}

/**
 * composer.lockから情報取得
 *
 * @param Model $model 呼び出し元Model
 * @param string $namespace Pluginのnamespace
 * @param string|array $filePath ファイルパスもしくはcomposer.lockファイルデータ
 * @return mixed array|bool
 */
	public function getComposer(Model $model, $namespace = null, $filePath = null) {
		if ($this->composers && ! $filePath) {
			$composers = $this->composers;
		} else {
			if ($filePath) {
				$file = new File($filePath);
			} else {
				$file = new File(ROOT . DS . 'composer.lock');
			}
			$contents = $file->read();
			$file->close();

			$packages = json_decode($contents, true);

			$composers = array();
			foreach ($packages['packages'] as $package) {
				if (preg_match('#^netcommons/#', $package['name'])) {
					$key = strtr(preg_replace('#^netcommons/#', '', $package['name']), '-', '_');
					$name = Inflector::humanize($key);
				} else {
					$key = $package['name'];
					$name = $package['name'];
				}

				$index = Hash::get($package, 'name');
				$composers[$index] = array(
					'key' => $key,
					'namespace' => Hash::get($package, 'name'),
					'description' => Hash::get($package, 'description'),
					'homepage' => Hash::get($package, 'homepage'),
					'version' => Hash::get($package, 'version'),
					'commit_version' => Hash::get($package, 'source.reference'),
					'source' => Hash::get($package, 'source.url', ''),
					'authors' => Hash::get($package, 'authors'),
					'license' => Hash::get($package, 'license'),
					'commited' => Hash::get($package, 'time'),
				);
				if (isset($package['extra']['installer-name'])) {
					$composers[$index]['name'] = $package['extra']['installer-name'];
				} else {
					$composers[$index]['name'] = $name;
				}
				if (isset($package['plugin-type'])) {
					$composers[$index]['type'] = $package['plugin-type'];
				} elseif (! preg_match('#^netcommons/#', $package['name'])) {
					$composers[$index]['type'] = Plugin::PLUGIN_TYPE_FOR_EXT_COMPOSER;
				}

				if (Hash::get($package, 'source.type') === 'git' &&
						$composers[$index]['source'] && $composers[$index]['commit_version']) {
					$composers[$index]['commit_url'] = preg_replace('/\.git$/', '', $composers[$index]['source']);
					$composers[$index]['commit_url'] .= '/tree/' . $composers[$index]['commit_version'];
				} else {
					$composers[$index]['commit_url'] = null;
				}
			}

			if (! $filePath) {
				$this->composers = $composers;
			}
		}

		if (! $namespace) {
			return $composers;
		}

		return Hash::get($composers, array($namespace));
	}

/**
 * bowerの情報取得
 *
 * @param Model $model 呼び出し元Model
 * @param string $namespace Pluginのnamespace
 * @param string $dirPath bowerのディレクトリ
 * @return mixed array|bool
 */
	public function getBower(Model $model, $namespace = null, $dirPath = null) {
		if ($this->bowers && ! $dirPath) {
			$bowers = $this->bowers;
		} elseif (is_array($dirPath)) {
			$bowers = array();
			foreach ($dirPath as $i => $package) {
				$bower = $this->_parseBower($package, null);
				$bowers[$bower['namespace']] = $bower;
			}
		} else {
			if ($dirPath) {
				$Folder = new Folder($dirPath);
			} else {
				$Folder = new Folder(WWW_ROOT . 'components');
			}
			$dirs = $Folder->read(Folder::SORT_NAME, false, true)[0];

			$bowers = array();
			foreach ($dirs as $i => $dir) {
				$file = new File($dir . DS . '.bower.json');
				$contents = $file->read();
				$file->close();
				$package = json_decode($contents, true);

				$bower = $this->_parseBower($package, $dir);
				$bowers[$bower['namespace']] = $bower;
			}

			if (! $dirPath) {
				$this->bowers = $bowers;
			}
		}

		if (! $namespace) {
			return $bowers;
		}

		return Hash::get($bowers, array($namespace));
	}

/**
 * bowerの情報をパースする
 *
 * @param array $package jsonファイルの情報
 * @return mixed array
 */
	protected function _parseBower($package, $dir) {
		$result = array(
			'name' => Hash::get($package, 'name'),
			'key' => strtr(Hash::get($package, 'name'), '.', '-'),
			'type' => Plugin::PLUGIN_TYPE_FOR_EXT_BOWER,
			'description' => Hash::get($package, 'description'),
			'homepage' => Hash::get($package, 'homepage'),
			'version' => Hash::get($package, 'version'),
			'commit_version' => Hash::get($package, '_resolution.commit'),
			'source' => Hash::get($package, '_source', ''),
			'authors' => Hash::get($package, 'authors'),
			'license' => Hash::get($package, 'license'),
		);

		if (! $result['version']) {
			$result['version'] = Hash::get($package, '_release');
		}
		$pattern = '/^' . preg_quote('https://github.com/', '/') . '|\.git$/';
		$result['namespace'] = preg_replace($pattern, '', $result['source']);

		if ($dir && file_exists($dir . DS . 'bower.json')) {
			$result['commited'] = date('Y-m-d H:i:s', filemtime($dir . DS . 'bower.json'));
		} else {
			$result['commited'] = null;
		}

		if (preg_match('/^' . preg_quote(Plugin::GITHUB_URL, '/') . '/', $result['source']) &&
				Hash::get($result, 'commit_version')) {
			$result['commit_url'] = preg_replace('/\.git$/', '', $result['source']);
			$result['commit_url'] .= '/tree/' . Hash::get($result, 'commit_version');
		} else {
			$result['commit_url'] = null;
		}
		return $result;
	}

/**
 * composer.lockファイルからバージョン情報をDBに更新する
 *
 * @param Model $model 呼び出し元Model
 * @param string|array $filePath ファイルパスもしくはcomposer.lockファイルデータ
 * @return bool
 */
	public function updateVersionByComposer(Model $model, $filePath = null) {
		$composers = $model->getComposer(null, $filePath);
		$this->_updateVersion($model, $composers);
		return true;
	}

/**
 * composer.lockファイルからバージョン情報をDBに更新する
 *
 * @param Model $model Model using this behavior
 * @param string $dirPath bowerのディレクトリ
 * @return bool
 */
	public function updateVersionByBower(Model $model, $dirPath = null) {
		$bowers = $model->getBower(null, $dirPath);
		$this->_updateVersion($model, $bowers);
		return true;
	}

/**
 * バージョン情報をDBに更新する
 *
 * @param Model $model 呼び出し元Model
 * @param array $packages パッケージリスト
 * @return bool
 */
	protected function _updateVersion(Model $model, $packages) {
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

/**
 * 更新があるかどうか
 *
 * @param Model $model Model using this behavior
 * @param string $dirPath bowerのディレクトリ
 * @return bool
 */
	public function hasUpdate(Model $model) {
		$model->loadModels([
			'Plugin' => 'PluginManager.Plugin',
		]);

		$composers = $model->getComposer();
		$bowers = $model->getBower();

		$latests = array_merge(
			Hash::combine($bowers, '{s}.key', '{s}.commit_version'),
			Hash::combine($composers, '{s}.key', '{s}.commit_version')
		);

		$currents = $model->Plugin->find('list', array(
			'recursive' => -1,
			'fields' => array('key', 'commit_version'),
			'conditions' => array(
				'language_id' => array(Current::read('Language.id'), '0'),
			),
		));

		return !empty(array_diff($latests, $currents));
	}

}
