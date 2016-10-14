<?php
/**
 * Plugin Model
 *
 * @property Language $Language
 * @property File $File
 * @property Role $Role
 * @property Room $Room
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * Plugin Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model
 */
class Plugin extends AppModel {

/**
 * PackagistのURL
 */
	const PACKAGIST_URL = 'https://packagist.org/packages/';

/**
 * GithubのURL
 */
	const GITHUB_URL = 'https://github.com/';

/**
 * コアプラグイン
 */
	const PLUGIN_TYPE_CORE = '0';

/**
 * フレームに設置するプラグイン
 */
	const PLUGIN_TYPE_FOR_FRAME = '1';

/**
 * コントロールパネルプラグイン
 * ※プラグイン管理でのタブ識別で使用する
 */
	const PLUGIN_TYPE_FOR_CONTROL_PANEL = '2';

/**
 * サイト管理者が操作できる管理プラグイン
 */
	const PLUGIN_TYPE_FOR_SITE_MANAGER = '2';

/**
 * システム管理者が操作できる管理プラグイン
 */
	const PLUGIN_TYPE_FOR_SYSTEM_MANGER = '3';

/**
 * 未インストール
 */
	const PLUGIN_TYPE_FOR_NOT_YET = '4';

/**
 * 外部ライブラリ composer
 */
	const PLUGIN_TYPE_FOR_EXT_COMPOSER = '5';

/**
 * 外部ライブラリ bower
 */
	const PLUGIN_TYPE_FOR_EXT_BOWER = '6';

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'PluginManager.Plugin',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'language_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'key' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'namespace' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		//'default_action' => array(
		//	'notBlank' => array(
		//		'rule' => array('notBlank'),
		//		//'message' => 'Your custom message here',
		//		//'allowEmpty' => false,
		//		//'required' => false,
		//		//'last' => false, // Stop validation after this rule
		//		//'on' => 'create', // Limit validation to 'create' or 'update' operations
		//	),
		//),
	);

	//The Associations below have been created with all possible keys,
	//those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Language' => array(
			'className' => 'M17n.Language',
			'foreignKey' => 'language_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * getMaxWeight
 *
 * @param int $type plugins.type
 * @return int
 */
	public function getMaxWeight($type) {
		$order = $this->find('first', array(
				'recursive' => -1,
				'fields' => array('weight'),
				'conditions' => array('type' => $type),
				'order' => array('weight' => 'DESC')
			));

		if (isset($order[$this->alias]['weight'])) {
			$weight = (int)$order[$this->alias]['weight'];
		} else {
			$weight = 0;
		}
		return $weight;
	}

/**
 * プラグインデータの取得
 *
 * @param int $type プラグインタイプ
 * @param string $key プラグインキー
 * @return array
 */
	public function getPlugins($type, $key = null) {
		$notLangTypes = array(
			self::PLUGIN_TYPE_CORE, self::PLUGIN_TYPE_FOR_EXT_COMPOSER, self::PLUGIN_TYPE_FOR_EXT_BOWER
		);
		if (! is_array($type) && in_array($type, $notLangTypes)) {
			$langId = '0';
		} else {
			$langId = Current::read('Language.id');
		}

		$conditions = array(
			'Plugin.type' => $type,
			'Plugin.language_id' => $langId
		);
		if (isset($key)) {
			$conditions['Plugin.key'] = $key;
			$order = array();
		} else {
			$order = array(
				$this->alias . '.weight' => 'asc',
				$this->alias . '.id' => 'asc'
			);
		}

		//pluginsテーブルの取得
		$plugins = $this->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'order' => $order,
		));
		if (! $plugins) {
			return array();
		}
		foreach ($plugins as $i => $plugin) {
			$plugin['Plugin']['serialize_data'] = unserialize($plugin['Plugin']['serialize_data']);
			if ($plugin['Plugin']['type'] === self::PLUGIN_TYPE_FOR_EXT_BOWER) {
				$plugin['Plugin']['package_url'] = Hash::get($plugin, 'Plugin.serialize_data.source');
				$plugin['latest'] = $this->getBower($plugin['Plugin']['namespace']);
			} else {
				$plugin['Plugin']['package_url'] = self::PACKAGIST_URL . $plugin['Plugin']['namespace'];
				$plugin['latest'] = $this->getComposer($plugin['Plugin']['namespace']);
			}
			$plugins[$i] = $plugin;
		}

		if ($key) {
			return Hash::get($plugins, '0', array());
		} else {
			return $plugins;
		}
	}

/**
 * 未インストールのプラグイン取得
 *
 * @param int $type プラグインタイプ
 * @param string $key プラグインキー
 * @return array
 */
	public function getNewPlugins($type, $key = null) {
		$conditions = array(
			'language_id' => array(Current::read('Language.id'), '0'),
		);

		if ($type === self::PLUGIN_TYPE_FOR_EXT_BOWER) {
			$packages = $this->getBower();
			$latests = Hash::extract($packages, '{s}.key', array());
			$conditions['type'] = self::PLUGIN_TYPE_FOR_EXT_BOWER;
		} elseif ($type === self::PLUGIN_TYPE_FOR_EXT_COMPOSER) {
			$packages = $this->getComposer();
			$notPackages = preg_replace('/-/', '_',
				preg_replace('/^netcommons\//', '', preg_grep('/^netcommons/', array_keys($packages)))
			);
			$latests = array_diff(Hash::extract($packages, '{s}.key', array()), $notPackages);
			$conditions['type'] = self::PLUGIN_TYPE_FOR_EXT_COMPOSER;
		} else {
			$packages = $this->getComposer();
			$latests = preg_replace('/-/', '_',
				preg_replace('/^netcommons\//', '', preg_grep('/^netcommons/', array_keys($packages)))
			);
			$conditions['type'] = array(
				self::PLUGIN_TYPE_CORE,
				self::PLUGIN_TYPE_FOR_FRAME,
				self::PLUGIN_TYPE_FOR_SITE_MANAGER,
				self::PLUGIN_TYPE_FOR_SYSTEM_MANGER,
			);
		}
		$packages = Hash::combine($packages, '{s}.key', '{s}');

		$currents = $this->find('list', array(
			'recursive' => -1,
			'fields' => array('key', 'commit_version'),
			'conditions' => $conditions,
		));
		$currents = array_keys($currents);

		$inserts = array_diff($latests, $currents);
		$plugins = array();
		if ($key) {
			if (in_array($key, $inserts, true)) {
				$plugin['Plugin'] = $packages[$key];
				$plugin['Plugin']['serialize_data'] = $packages[$key];
				$plugin['latest'] = $packages[$key];

				//$plugin['Plugin']['serialize_data'] = $packages[$pluginKey];
				if (Hash::get($plugin['Plugin'], 'type') === self::PLUGIN_TYPE_FOR_EXT_BOWER) {
					$plugin['Plugin']['package_url'] = Hash::get($packages[$pluginKey], 'source');
				} else {
					$plugin['Plugin']['package_url'] = self::PACKAGIST_URL . $plugin['Plugin']['namespace'];
				}
				$plugins[] = $plugin;
			}
		} else {
			$index = 0;
			foreach ($inserts as $pluginKey) {
				$plugin['Plugin'] = $packages[$pluginKey];
				$plugin['latest'] = $packages[$pluginKey];
				//$plugin['Plugin']['serialize_data'] = $packages[$pluginKey];
				if (Hash::get($plugin['Plugin'], 'type') === self::PLUGIN_TYPE_FOR_EXT_BOWER) {
					$plugin['Plugin']['package_url'] = Hash::get($packages[$pluginKey], 'source');
				} else {
					$plugin['Plugin']['package_url'] = self::PACKAGIST_URL . $plugin['Plugin']['namespace'];
				}

				$plugins[$index] = $plugin;
				$index++;
			}
		}

		if ($key) {
			return Hash::get($plugins, '0');
		} else {
			return $plugins;
		}
	}

/**
 * プラグインの表示順序更新
 *
 * @param array $data Request data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function saveWeight($data) {
		$this->loadModels([
			'Plugin' => 'PluginManager.Plugin',
		]);

		//トランザクションBegin
		$this->begin();

		try {
			//Pluginテーブルの登録
			$fieldList = array('weight');
			foreach ($data['Plugins'] as $req) {
				$plugins = $this->find('all', array(
					'recursive' => -1,
					'conditions' => array('key' => $req['Plugin']['key']),
				));
				foreach ($plugins as $plugin) {
					if ($plugin['Plugin']['weight'] === $req['Plugin']['weight']) {
						continue;
					}
					unset($plugin['Plugin']['modified_user'], $plugin['Plugin']['modified']);

					$plugin['Plugin']['weight'] = (int)$req['Plugin']['weight'];
					if (! $this->save($plugin, array('fieldList' => $fieldList))) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				}
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * バージョンアップを実行
 *
 * @param string $plugin Plugin key
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function runVersionUp($plugin) {
		try {
			//トランザクションBegin
			$this->begin();

			if (! Hash::get($plugin, 'latest') && Hash::get($plugin, 'Plugin.id')) {
				if (! $this->uninstallPlugin(Hash::get($plugin, 'Plugin.key'))) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				$this->deletePackageDir($plugin);
			} else {
				if (Hash::get($plugin, 'latest.packageType') === 'cakephp-plugin') {
					if (! $this->runMigration(Hash::get($plugin, 'latest.key'))) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				}
				if (! $this->updateVersion(array(Hash::get($plugin, 'latest')))) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				if (Hash::get($plugin, 'latest.originalSource') !==
						Hash::get($plugin, 'Plugin.serialize_data.originalSource')) {
					$this->deletePackageDir($plugin);
				}
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * Pluginのアンインストール
 *
 * @param array $data Pluginデータ
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function uninstallPlugin($data) {
		$this->loadModels([
			'PluginsRole' => 'PluginManager.PluginsRole',
			'PluginsRoom' => 'PluginManager.PluginsRoom',
		]);

		//トランザクションBegin
		$this->begin();

		if (is_string($data)) {
			$key = $data;
		} else {
			$key = $data[$this->alias]['key'];
		}

		try {
			//Pluginの削除
			if (! $this->deleteAll(array($this->alias . '.key' => $key), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoomの削除
			$conditions = array($this->PluginsRoom->alias . '.plugin_key' => $key);
			if (! $this->PluginsRoom->deleteAll($conditions, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoleの削除
			$conditions = array($this->PluginsRole->alias . '.plugin_key' => $key);
			if (! $this->PluginsRole->deleteAll($conditions, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * 更新があるかどうか
 *
 * @return bool
 */
	public function hasUpdate() {
		$composers = $this->getComposer();
		$bowers = $this->getBower();

		$latests = array_merge(
			Hash::combine($bowers, '{s}.key', '{s}.commit_version'),
			Hash::combine($composers, '{s}.key', '{s}.commit_version')
		);

		$currents = $this->find('list', array(
			'recursive' => -1,
			'fields' => array('key', 'commit_version'),
			'conditions' => array(
				'language_id' => array(Current::read('Language.id'), '0'),
			),
		));

		return !empty(array_diff($latests, $currents));
	}

}
