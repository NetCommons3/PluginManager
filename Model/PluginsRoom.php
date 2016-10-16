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
App::uses('Plugin', 'PluginManager.Model');

/**
 * PluginsRoom Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model
 */
class PluginsRoom extends AppModel {

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'PluginManager.PluginsRoom',
	);

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
 * @return mixed array or false
 */
	public function getPlugins($roomId) {
		//ルームIDのセット
		$roomId = (int)$roomId;
		if (! $roomId) {
			return false;
		}

		//plugins_languagesテーブルの取得
		$this->belongsTo['Plugin']['conditions']['Plugin.language_id'] = Current::read('Language.id');

		//pluginsテーブルの取得
		$plugins = $this->find('all', array(
			'conditions' => array(
				'Plugin.type' => Plugin::PLUGIN_TYPE_FOR_FRAME,
				/* 'Plugin.language_id' => $langId, */
				'Room.id' => $roomId
			),
			'order' => $this->alias . '.id',
		));

		return $plugins;
	}

}
