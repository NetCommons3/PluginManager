<?php
/**
 * PluginFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * PluginFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Fixture
 */
class PluginFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Key to identify plugin. Must be equivalent to plugin name used in router url.  e.g.) user_manager, auth, pages', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Human friendly name for the plugin.  e.g.) User Manager, Auth, Pages', 'charset' => 'utf8'),
		'namespace' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Unique namespace for package management system.  e.g.) packagist', 'charset' => 'utf8'),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'Display order.'),
		'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '1:for frame,2:for control panel'),
		'default_action' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Default action for content rendering', 'charset' => 'utf8'),
		'default_setting_action' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Default action for frame settings', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		//一般プラグイン
		array(
			'id' => 1,
			'language_id' => 1,
			'key' => 'test_plugin',
			'name' => 'Lorem ipsum dolor sit amet',
			'namespace' => 'Lorem ipsum dolor sit amet',
			'weight' => 1,
			'type' => 1,
			'default_action' => '',
			'default_setting_action' => '',
		),
		array(
			'id' => 2,
			'language_id' => 2,
			'key' => 'test_plugin',
			'name' => 'Lorem ipsum dolor sit amet',
			'namespace' => 'Lorem ipsum dolor sit amet',
			'weight' => 1,
			'type' => 1,
			'default_action' => '',
			'default_setting_action' => '',
		),
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

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		if (class_exists('NetCommonsTestSuite') && NetCommonsTestSuite::$plugin) {
			$records = array_keys($this->records);
			foreach ($records as $i) {
				if ($this->records[$i]['key'] !== 'test_plugin') {
					continue;
				}
				$this->records[$i]['key'] = NetCommonsTestSuite::$plugin;
			}
		}
		parent::init();
	}

}
