<?php
/**
 * Plugin Model
 *
 * @property Language $Language
 * @property File $File
 * @property Role $Role
 * @property Room $Room
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('AppModel', 'Model');

/**
 * Summary for Plugin Model
 */
class Plugin extends AppModel {

/**
 * constant value for not yet
 */
	const PLUGIN_TYPE_FOR_NOT_YET = '0';

/**
 * constant value for frame
 */
	const PLUGIN_TYPE_FOR_FRAME = '1';

/**
 * constant value for control panel
 */
	const PLUGIN_TYPE_FOR_CONTROL_PANEL = '2';

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
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'namespace' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		//'default_action' => array(
		//	'notEmpty' => array(
		//		'rule' => array('notEmpty'),
		//		//'message' => 'Your custom message here',
		//		//'allowEmpty' => false,
		//		//'required' => false,
		//		//'last' => false, // Stop validation after this rule
		//		//'on' => 'create', // Limit validation to 'create' or 'update' operations
		//	),
		//),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
 * get plugins for select box options
 *
 * @param array $options find options
 * @return array select box options
 */
	public function getForOptions($options) {
		$options = Hash::merge(['recursive' => -1], $options);

		$plugins = $this->find('all', $options);
		$map = [];
		foreach ($plugins as $plugin) {
			$map[$plugin[$this->alias]['key']] = $plugin[$this->alias]['name'];
		}

		return $map;
	}

/**
 * get plugins for select box options
 *
 * @param array $options find options
 * @return array select box options
 */
	public function getKeyIndexedHash($options) {
		$options = Hash::merge(['recursive' => -1], $options);

		$plugins = $this->find('all', $options);
		$map = [];
		foreach ($plugins as $plugin) {
			$map[$plugin[$this->alias]['key']] = $plugin;
		}

		return $map;
	}

/**
 * Get plugin data from type and roleId, $langId
 *
 * @param int $type array|int 1:for frame/2:for controll panel
 * @param int $langId languages.id
 * @return mixed array|bool
 */
	public function getPlugins($type, $langId) {
		//pluginsテーブルの取得
		$plugins = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'Plugin.type' => $type,
				'Plugin.language_id' => (int)$langId
			),
			'order' => array($this->alias . '.weight' => 'asc', $this->alias . '.id' => 'desc'),
		));

		return $plugins;
	}

/**
 * Save plugin
 *
 * @param array $data Plugin data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function savePlugin($data) {
		$this->loadModels([
			'Plugin' => 'PluginManager.Plugin',
			'PluginsRole' => 'PluginManager.PluginsRole',
			'PluginsRoom' => 'PluginManager.PluginsRoom',
			'Language' => 'M17n.Language',
		]);

		//トランザクションBegin
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		//言語データ取得
		$languages = $this->Language->find('list', array(
			'fields' => array('Language.code', 'Language.id')
		));

		$currentLang = Configure::read('Config.language');

		try {
			//Pluginテーブルの登録
			foreach (Configure::read('Config.languageEnabled') as $lang) {
				$conditions = array(
					'Plugin.language_id' => $languages[$lang],
					'Plugin.key' => $data['Plugin']['key'],
				);

				if (! $plugin = $this->Plugin->find('first', array(
					'recursive' => -1,
					'conditions' => $conditions,
				))) {
					$plugin = $this->Plugin->create(array('id' => null));
				}

				Configure::write('Config.language', $lang);

				$plugin['Plugin'] = Hash::merge($data['Plugin'], array(
					'language_id' => $languages[$lang],
					'name' => __d($data['Plugin']['key'], $data['Plugin']['name'])
				));

				$this->Plugin->save($plugin, false);
			}

			Configure::write('Config.language', $currentLang);

			//PluginsRoleテーブルの登録
			if (isset($data['PluginsRole'])) {
				$this->PluginsRole->savePluginRoles($data);
			}

			//PluginsRoomテーブルの登録
			if (isset($data['PluginsRoom'])) {
				$this->PluginsRoom->savePluginRooms($data);
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
 * Save plugin
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
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		try {
			//Pluginテーブルの登録
			$fieldList = array('weight');
			foreach ($data as $req) {
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
 * Delete plugin
 *
 * @param array $data Plugin data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function deletePlugin($data) {
		$this->loadModels([
			'Plugin' => 'PluginManager.Plugin',
			'PluginsRole' => 'PluginManager.PluginsRole',
			'PluginsRoom' => 'PluginManager.PluginsRoom',
		]);

		//トランザクションBegin
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		try {
			//Pluginの削除
			if (! $this->deleteAll(array($this->alias . '.key' => $data[$this->alias]['key']), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoomの削除
			if (! $this->PluginsRoom->deleteAll(array($this->PluginsRoom->alias . '.plugin_key' => $data[$this->alias]['key']), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//PluginsRoleの削除
			if (! $this->PluginsRole->deleteAll(array($this->PluginsRole->alias . '.plugin_key' => $data[$this->alias]['key']), false)) {
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

}
