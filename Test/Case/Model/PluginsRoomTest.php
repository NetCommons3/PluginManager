<?php
/**
 * PluginsRoom Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginsRoom', 'PluginManager.Model');
App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * PluginsRoom Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model
 */
class PluginsRoomTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.plugin_manager.plugin',
		'plugin.plugin_manager.plugins_room',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PluginsRoom = ClassRegistry::init('PluginManager.PluginsRoom');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->PluginsRoom);
		parent::tearDown();
	}

/**
 * testIndex
 *
 * @return void
 */
	public function testIndex() {
		$this->assertTrue(true);
	}

/**
 * testGetPlugins
 *
 * @return void
 */
	public function testGetPlugins() {
		$roomId = 1;
		$plugins = $this->PluginsRoom->getPlugins($roomId);

		$this->assertTrue(is_array($plugins));
	}

/**
 * testGetPluginsError
 *
 * @return void
 */
	public function testGetPluginsRoomIdError() {
		$checks = array(
			array('roomId' => null),
			array('roomId' => 0),
			array('roomId' => 'aaaa'),
		);
		foreach ($checks as $check) {
			$roomId = $check['roomId'];
			$plugins = $this->PluginsRoom->getPlugins($roomId);

			$this->assertFalse($plugins);
		}
	}

}
