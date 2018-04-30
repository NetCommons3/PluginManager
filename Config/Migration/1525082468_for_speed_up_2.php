<?php
/**
 * 速度改善のためのインデックス見直し
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 速度改善のためのインデックス見直し
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Config\Migration
 */
class ForSpeedUp2 extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'for_speed_up_2';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				'plugins_roles' => array('indexes' => array('role_key')),
			),
			'create_field' => array(
				'plugins_roles' => array(
					'indexes' => array(
						'role_key' => array('column' => array('role_key', 'plugin_key'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'create_field' => array(
				'plugins_roles' => array(
					'indexes' => array(
						'role_key' => array(),
					),
				),
			),
			'drop_field' => array(
				'plugins_roles' => array('indexes' => array('role_key')),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
