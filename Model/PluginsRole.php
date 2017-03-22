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
 * @package NetCommons\PluginManager\Model
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
 * RoleKeyに対するプラグインデータ取得
 *
 * @param mixed $pluginType array|int プラグインタイプ
 * @param int $roleKey ロールKey
 * @param string $joinType JOINタイプ(LEFT or INNER)
 * @param string $queryType クエリタイプ(all or first)
 * @return mixed array|bool
 */
	public function getPlugins($pluginType, $roleKey, $joinType = 'LEFT', $queryType = 'all') {
		if (! $roleKey) {
			return false;
		}

		$plugins = $this->Plugin->find($queryType, array(
			'recursive' => -1,
			'fields' => array(
				$this->alias . '.*',
				$this->Plugin->alias . '.*',
			),
			'joins' => array(
				array(
					'table' => $this->table,
					'alias' => $this->alias,
					'type' => $joinType,
					'conditions' => array(
						$this->Plugin->alias . '.key' . ' = ' . $this->alias . ' .plugin_key',
						$this->alias . '.role_key' => $roleKey,
					),
				),
			),
			'conditions' => array(
				$this->Plugin->alias . '.type' => $pluginType,
				$this->Plugin->alias . '.language_id' => Current::read('Language.id'),
			),
			'order' => array(
				$this->Plugin->alias . '.weight' => 'asc',
				$this->Plugin->alias . '.id' => 'desc'
			),
		));

		return $plugins;
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
