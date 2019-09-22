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
class Plugin4managerFixture extends PluginFixture {

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
		//サイト管理プラグイン
		array(
			'id' => 3,
			'language_id' => 2,
			'key' => 'test_plugin',
			'name' => 'Lorem ipsum dolor sit amet',
			'namespace' => 'Lorem ipsum dolor sit amet',
			'weight' => 1,
			'type' => 2,
			'default_action' => '',
			'default_setting_action' => '',
		),
		//システム管理プラグイン
		array(
			'id' => 4,
			'language_id' => 2,
			'key' => 'test_plugin',
			'name' => 'Lorem ipsum dolor sit amet',
			'namespace' => 'Lorem ipsum dolor sit amet',
			'weight' => 1,
			'type' => 3,
			'default_action' => '',
			'default_setting_action' => '',
		),
	);

}
