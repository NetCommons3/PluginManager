<?php
/**
 * PluginFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginFixture', 'PluginManager.Test/Fixture');

/**
 * PluginFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Fixture
 */
class Plugin4testFixture extends PluginFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Plugin';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'plugins';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		//プラグイン管理
		array(
			'id' => '1',
			'language_id' => '1',
			'key' => 'plugin_manager',
			'name' => 'Plugin Manager',
			'namespace' => 'netcommons/plugin-manager',
			'weight' => '1',
			'type' => '4',
			'default_action' => '',
			'default_setting_action' => '',
		),
		array(
			'id' => '2',
			'language_id' => '2',
			'key' => 'plugin_manager',
			'name' => 'Plugin Manager',
			'namespace' => 'netcommons/plugin-manager',
			'weight' => '1',
			'type' => '4',
			'default_action' => '',
			'default_setting_action' => '',
		),
		//システム管理
		array(
			'id' => '3',
			'language_id' => '2',
			'key' => 'system_manager',
			'name' => 'System Manager',
			'namespace' => 'netcommons/system-manager',
			'weight' => '2',
			'type' => '4',
			'default_action' => '',
			'default_setting_action' => '',
		),
		//会員管理
		array(
			'id' => '4',
			'language_id' => '2',
			'key' => 'user_manger',
			'name' => 'User Manager',
			'namespace' => 'netcommons/user-manager',
			'weight' => '1',
			'type' => '3',
			'default_action' => '',
			'default_setting_action' => '',
		),
		//ルーム管理
		array(
			'id' => '5',
			'language_id' => '2',
			'key' => 'rooms',
			'name' => 'Room Manager',
			'namespace' => 'netcommons/rooms',
			'weight' => '2',
			'type' => '3',
			'default_action' => '',
			'default_setting_action' => '',
		),
		//権限管理
		array(
			'id' => '6',
			'language_id' => '2',
			'key' => 'user_roles',
			'name' => 'UserRole Manager',
			'namespace' => 'netcommons/user-roles',
			'weight' => '3',
			'type' => '3',
			'default_action' => '',
			'default_setting_action' => '',
		),
		//お知らせ
		array(
			'id' => '7',
			'language_id' => '2',
			'key' => 'announcements',
			'name' => 'Announcements',
			'namespace' => 'netcommons/announcements',
			'weight' => '1',
			'type' => '1',
			'default_action' => '',
			'default_setting_action' => '',
		),
		//掲示板
		array(
			'id' => '8',
			'language_id' => '2',
			'key' => 'bbses',
			'name' => 'BBS',
			'namespace' => 'netcommons/bbses',
			'weight' => '2',
			'type' => '1',
			'default_action' => '',
			'default_setting_action' => '',
		),
		//NetCommons
		array(
			'id' => '9',
			'language_id' => '0',
			'key' => 'net-commons',
			'name' => 'NetCommons',
			'namespace' => 'netcommons/net-commons',
			'weight' => '1',
			'type' => '0',
			'default_action' => '',
			'default_setting_action' => '',
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		parent::init();
	}

}
