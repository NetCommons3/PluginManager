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
		$this->belongsTo['Plugin']['conditions']['Plugin.language_id'] = (int)$langId;

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

		//plugins_languagesテーブルの取得
		$this->belongsTo['Plugin']['conditions']['Plugin.language_id'] = (int)$langId;

		//pluginsテーブルの取得
		$plugin = $this->find('first', array(
			'conditions' => array(
				'Plugin.key' => $key,
				'Role.id' => (int)$roleId
			)
		));

		return $plugin;
	}

/**
 * Save plugin roles
 * Here does not transaction. Please do the transaction and validation in the caller.
 *
 * @param array $data Plugin roles data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function savePluginRoles($data) {
		//PluginsRoleテーブルの登録
		foreach ($data['PluginsRole'] as $pluginRole) {
			$conditions = array(
				'role_key' => $pluginRole['role_key'],
				'plugin_key' => $data['Plugin']['key'],
			);

			$count = $this->find('count', array(
				'recursive' => -1,
				'conditions' => $conditions,
			));
			if ($count > 0) {
				continue;
			}

			$pluginRole = Hash::merge($pluginRole, $conditions);
			$this->create();
			if (! $this->save($pluginRole, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		return true;
	}

}
