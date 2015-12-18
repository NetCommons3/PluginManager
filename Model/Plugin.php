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
 * PackagistのURL
 */
	const PACKAGIST_URL = 'https://packagist.org/packages/';

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
 * 外部ライブラリ
 */
	const PLUGIN_TYPE_FOR_EXTERNAL = '5';

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'PluginManager.Bower',
		'PluginManager.Composer',
		'PluginManager.Migration',
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
 * Get plugin data from type and roleId, $langId
 *
 * @param int $type array|int 1:for frame/2:for controll panel
 * @param string $key plugins.key
 * @return mixed array|bool
 */
	public function getPlugins($type, $key = null) {
		$conditions = array(
			'Plugin.type' => $type,
			'Plugin.language_id' => Current::read('Language.id')
		);
		if (isset($key)) {
			$conditions['Plugin.key'] = $key;
			$order = array();
		} else {
			$order = array(
				$this->alias . '.weight' => 'asc',
				$this->alias . '.id' => 'desc'
			);
		}

		//pluginsテーブルの取得
		if (! $plugins = $this->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'order' => $order,
		))) {
			return null;
		}

		foreach ($plugins as $i => $plugin) {
			$plugins[$i]['composer'] = $this->getComposer($plugin['Plugin']['namespace']);
		}

		return $plugins;
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

}
