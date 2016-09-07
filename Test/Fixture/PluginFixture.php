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
		require_once App::pluginPath('PluginManager') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new PluginManagerSchema())->tables[Inflector::tableize($this->name)];

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
