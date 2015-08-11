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

/**
 * PluginsRoom Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model
 */
class PluginsRoomTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		//'plugin.m17n.language',
		'plugin.plugin_manager.plugin',
		'plugin.plugin_manager.plugins_room',
		//'plugin.rooms.room',
		//'plugin.users.user',
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
		$langId = 2;
		$plugins = $this->PluginsRoom->getPlugins($roomId, $langId);

		$this->assertTrue(is_array($plugins));
	}

/**
 * testGetPluginsError
 *
 * @return void
 */
	public function testGetPluginsRoomIdError() {
		$checks = array(
			array('roomId' => null, 'langId' => 2),
			array('roomId' => 0, 'langId' => 2),
			array('roomId' => 'aaaa', 'langId' => 2),
		);
		foreach ($checks as $check) {
			$roomId = $check['roomId'];
			$langId = $check['langId'];
			$plugins = $this->PluginsRoom->getPlugins($roomId, $langId);

			$this->assertFalse($plugins, print_r($check, true));
		}
	}

/**
 * testGetPluginsIrregular
 *
 * @return void
 */
	public function testGetPluginsLangIdError() {
		$checks = array(
			array('roomId' => 1, 'langId' => null),
			array('roomId' => 1, 'langId' => 0),
			array('roomId' => 1, 'langId' => 'aaaa'),
		);
		foreach ($checks as $check) {
			$roomId = $check['roomId'];
			$langId = $check['langId'];
			$plugins = $this->PluginsRoom->getPlugins($roomId, $langId);

			$this->assertFalse(isset($plugins['Plugin']), print_r($check, true));
		}
	}

}
