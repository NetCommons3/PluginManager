<?php
/**
 * PluginsRole Model
 *
 * @property Role $Role
 * @property Plugin $Plugin
 * @property LanguagesPlugin $LanguagesPlugin
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * PluginsRole Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Roles\Model
 */
class PluginsRole extends AppModel {

/**
 * constant value for frame
 */
	const PLUGIN_TYPE_FOR_FRAME = '1';

/**
 * constant value for control panel
 */
	const PLUGIN_TYPE_FOR_CONTROL_PANEL = '2';

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Role' => array(
			'className' => 'Roles.Role',
			'foreignKey' => false,
			'conditions' => array('PluginsRole.role_key = Role.key'),
			'fields' => '',
			'order' => ''
		),
		'Plugin' => array(
			'className' => 'PluginManager.Plugin',
			'foreignKey' => false,
			'conditions' => array('PluginsRole.plugin_key = Plugin.key'),
			'fields' => '',
			'order' => ''
		),
	);

/**
 * Get plugin data from type and roleId, $langId
 *
 * @param mixed $type array|int 1:for frame/2:for controll panel
 * @param int $roleKey roles.key
 * @param int $langId languages.id
 * @return mixed array|bool
 */
	public function getPlugins($type, $roleKey, $langId) {
		if (! $roleKey || ! $langId) {
			return false;
		}

		//ロールIDのセット
		//$roleId = (int)$roleId;

		//plugins_languagesテーブルの取得
		$langId = (int)$langId;
		$this->belongsTo['Plugin']['conditions']['Plugin.language_id'] = $langId;

		//pluginsテーブルの取得
		$plugins = $this->find('all', array(
			'conditions' => array(
				'Plugin.type' => $type,
				'Role.key' => $roleKey
			),
			'order' => $this->name . '.id',
		));

		return $plugins;
	}

/**
 * Get plugin data from folder and roomId, langId
 *
 * @param int $key plugins.folder
 * @param int $roleId roles.id
 * @param int $langId languages.id
 * @return int blocks.id
 */
	public function getPluginByKey($key, $roleId, $langId) {
		if (! $roleId || ! $langId) {
			return false;
		}

		//ロールIDのセット
		$roleId = (int)$roleId;

		//plugins_languagesテーブルの取得
		$langId = (int)$langId;
		$this->belongsTo['Plugin']['conditions']['Plugin.language_id'] = $langId;

		//pluginsテーブルの取得
		$plugin = $this->find('first', array(
			'conditions' => array(
				'Plugin.key' => $key,
				'Role.id' => $roleId
			)
		));

		return $plugin;
	}

}
