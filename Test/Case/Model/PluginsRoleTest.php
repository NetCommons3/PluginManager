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

/**
 * PluginsRole Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model
 */
class PluginsRoleTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		//'plugin.m17n.language',
		'plugin.plugin_manager.plugin',
		'plugin.plugin_manager.plugins_role',
		//'plugin.roles.role',
		//'plugin.users.user',
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
		$langId = 2;
		$type = Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL;
		$plugins = $this->PluginsRole->getPlugins($type, $roleKey, $langId);
		$this->assertCount(1, $plugins);

		$expected = array(
			'PluginsRole', 'Role', 'Plugin', 'TrackableCreator', 'TrackableUpdater'
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
		$langId = 0;
		$type = Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL;
		$plugins = $this->PluginsRole->getPlugins($type, $roleId, $langId);

		$this->assertFalse($plugins);
	}

/**
 * testGetPluginByFolder
 *
 * @return void
 */
	public function testGetPluginByKey() {
		$roleId = 1;
		$langId = 2;
		$key = 'roles';
		$plugins = $this->PluginsRole->getPluginByKey($key, $roleId, $langId);

		$expected = array(
			'PluginsRole', 'Role', 'Plugin', 'TrackableCreator', 'TrackableUpdater'
		);
		$result = array_keys($plugins);
		$this->assertEquals($expected, $result);
	}

/**
 * testGetPluginByFolderError
 *
 * @return void
 */
	public function testGetPluginByKeyError() {
		$roleId = 0;
		$langId = 0;
		$key = 'roles';
		$plugins = $this->PluginsRole->getPluginByKey($key, $roleId, $langId);

		$this->assertFalse($plugins);
	}

}
