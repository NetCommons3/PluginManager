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
 * Themesのファイル取得
 *
 * @var array
 */
	public $themes = array();

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
				$index = Hash::get($package, 'name');
				$composers[$index] = $this->_parseComposer($package);
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
 * bowerの情報をパースする
 *
 * @param array $package jsonファイルの情報
 * @return mixed array
 */
	protected function _parseComposer($package) {
		if (preg_match('#^netcommons/#', $package['name'])) {
			$key = strtr(preg_replace('#^netcommons/#', '', $package['name']), '-', '_');
			$name = Inflector::humanize($key);
			$originalSource = Inflector::camelize($key);
		} elseif (Hash::get($package, 'type') === 'cakephp-plugin') {
			$key = strtr(substr($package['name'], strrpos($package['name'], '/') + 1), '-', '_');
			$name = Inflector::humanize(strtr($package['name'], '/', ' '));
			$originalSource = Inflector::camelize($key);
		} else {
			$key = $package['name'];
			$name = $package['name'];
			$originalSource = $package['name'];
		}

		$result = array(
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
			'packageType' => Hash::get($package, 'type'),
			'originalSource' => $originalSource
		);
		if (isset($package['extra']['installer-name'])) {
			$result['key'] = Inflector::underscore($package['extra']['installer-name']);
			$result['name'] = $package['extra']['installer-name'];
			$result['originalSource'] = $package['extra']['installer-name'];
		} else {
			$result['name'] = $name;
		}
		if (isset($package['plugin-type'])) {
			$result['type'] = $package['plugin-type'];
		} elseif (! preg_match('#^netcommons/#', $package['name'])) {
			$result['type'] = Plugin::PLUGIN_TYPE_FOR_EXT_COMPOSER;
		}

		if (Hash::get($package, 'source.type') === 'git' &&
				$result['source'] && $result['commit_version']) {
			$result['commit_url'] = preg_replace('/\.git$/', '', $result['source']);
			$result['commit_url'] .= '/tree/' . $result['commit_version'];
		} else {
			$result['commit_url'] = null;
		}

		return $result;
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
			foreach ($dirPath as $package) {
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
			foreach ($dirs as $dir) {
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
 * @param string $dir ディレクトリパス
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
			'packageType' => 'bower',
			'originalSource' => Hash::get($package, '_originalSource', Hash::get($package, 'name')),
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
 * themeの情報取得
 *
 * @param Model $model 呼び出し元Model
 * @param string $namespace Pluginのnamespace
 * @param string $dirPath themeのディレクトリ
 * @return mixed array|bool
 */
	public function getTheme(Model $model, $namespace = null, $dirPath = null) {
		if ($this->themes && ! $dirPath) {
			$themes = $this->themes;
		} else {
			if ($dirPath) {
				$Folder = new Folder($dirPath);
			} else {
				$Folder = new Folder(APP . 'View' . DS . 'Themed');
			}
			$dirs = $Folder->read(Folder::SORT_NAME, false, true)[0];

			$themes = array();
			foreach ($dirs as $dir) {
				$file = new File($dir . DS . 'theme.json');
				$contents = $file->read();
				$file->close();
				$package = json_decode($contents, true);

				$theme = $this->_parseTheme($package, $dir);
				$themes[$theme['namespace']] = $theme;
			}

			if (! $dirPath) {
				$this->themes = $themes;
			}
		}

		if (! $namespace) {
			return $themes;
		}

		return Hash::get($themes, array($namespace));
	}

/**
 * bowerの情報をパースする
 *
 * @param array $package jsonファイルの情報
 * @param string $dir ディレクトリパス
 * @return mixed array
 */
	protected function _parseTheme($package, $dir) {
		$result = array(
			'name' => Hash::get($package, 'name'),
			'key' => Inflector::underscore(basename($dir)),
			'namespace' => basename($dir),
			'type' => Plugin::PLUGIN_TYPE_FOR_THEME,
			'description' => Hash::get($package, 'description'),
			'homepage' => Hash::get($package, 'homepage'),
			'version' => Hash::get($package, 'version'),
			'commit_version' => Hash::get($package, 'source.reference', Hash::get($package, 'version')),
			'source' => Hash::get($package, 'source.url', ''),
			'authors' => Hash::get($package, 'authors'),
			'license' => Hash::get($package, 'license'),
			'commited' => Hash::get($package, 'time'),
			'packageType' => Hash::get($package, 'type'),
			'originalSource' => basename($dir)
		);

		if (Hash::get($package, 'source.type') === 'git' &&
				$result['source'] && $result['commit_version']) {
			$result['commit_url'] = preg_replace('/\.git$/', '', $result['source']);
			$result['commit_url'] .= '/tree/' . $result['commit_version'];
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
		$this->updateVersion($model, $composers);
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
		$this->updateVersion($model, $bowers);
		return true;
	}

/**
 * composer.lockファイルからバージョン情報をDBに更新する
 *
 * @param Model $model Model using this behavior
 * @param string $dirPath themeのディレクトリ
 * @return bool
 */
	public function updateVersionByTheme(Model $model, $dirPath = null) {
		$themes = $model->getTheme(null, $dirPath);
		$this->updateVersion($model, $themes);
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
