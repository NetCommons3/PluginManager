<?php
/**
 * PluginsRoom Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * PluginsRoom Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model
 */
class PluginsRoom extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Plugin' => array(
			'className' => 'PluginManager.Plugin',
			'foreignKey' => false,
			'type' => 'inner',
			'conditions' => array('PluginsRoom.plugin_key = Plugin.key'),
		),
		'Room' => array(
			'className' => 'Rooms.Room',
			'foreignKey' => 'room_id',
			'type' => 'inner',
		),
	);

/**
 * Get plugin data from type and roomId, $langId
 *
 * @param int $roomId rooms.id
 * @param int $langId languages.id
 * @return mixed array or false
 */
	public function getPlugins($roomId, $langId) {
		//ルームIDのセット
		$roomId = (int)$roomId;
		//言語IDのセット
		$langId = (int)$langId;

		if (! $roomId) {
			return false;
		}

		//plugins_languagesテーブルの取得
		$this->belongsTo['Plugin']['conditions']['Plugin.language_id'] = $langId;

		//pluginsテーブルの取得
		$Plugin = $this->Plugin;
		$plugins = $this->find('all', array(
			'conditions' => array(
				'Plugin.type' => $Plugin::PLUGIN_TYPE_FOR_FRAME,
				/* 'Plugin.language_id' => $langId, */
				'Room.id' => $roomId
			),
			'order' => $this->name . '.id',
		));

		return $plugins;
	}

/**
 * Save plugin rooms
 * Here does not transaction. Please do the transaction and validation in the caller.
 *
 * @param array $data Plugin rooms data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function savePluginRooms($data) {
		//PluginsRoleテーブルの登録
		foreach ($data['PluginsRoom'] as $pluginsRoom) {
			$conditions = array(
				'room_id' => $pluginsRoom['room_id'],
				'plugin_key' => $data['Plugin']['key'],
			);

			$count = $this->find('count', array(
				'recursive' => -1,
				'conditions' => $conditions,
			));
			if ($count > 0) {
				continue;
			}

			$pluginsRoom = Hash::merge($pluginsRoom, $conditions);
			$this->create();
			if (! $this->save($pluginsRoom, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		return true;
	}

}
