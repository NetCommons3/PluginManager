<?php
/**
 * PluginsRole Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginsRole', 'PluginManager.Model');
App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * PluginsRole Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model
 */
class PluginsRoleTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.plugin_manager.plugin',
		'plugin.plugin_manager.plugins_role',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PluginsRole = ClassRegistry::init('PluginManager.PluginsRole');
		$this->Plugin = ClassRegistry::init('PluginManager.Plugin');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->PluginsRole);
		parent::tearDown();
	}

/**
 * testGetPluginData
 *
 * @return void
 */
	public function testGetPlugins() {
		$roleKey = 'system_administrator';
		$type = Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL;
		$plugins = $this->PluginsRole->getPlugins($type, $roleKey);
		$this->assertCount(1, $plugins);

		$expected = array(
			'PluginsRole', 'Role', 'Plugin'
		);
		$result = array_keys($plugins[0]);
		$this->assertEquals($expected, $result);
	}

/**
 * testGetPluginsError
 *
 * @return void
 */
	public function testGetPluginsError() {
		$roleId = 0;
		$type = Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL;
		$plugins = $this->PluginsRole->getPlugins($type, $roleId);

		$this->assertFalse($plugins);
	}

}
