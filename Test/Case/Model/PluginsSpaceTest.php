<?php
/**
 * PluginsSpace Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginsSpace', 'PluginManager.Model');

/**
 * PluginsSpace Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model
 */
class PluginsSpaceTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.plugin_manager.plugins_space',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PluginsSpace = ClassRegistry::init('PluginManager.PluginsSpace');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->PluginsSpace);

		parent::tearDown();
	}

/**
 * testGet
 *
 * @return void
 */
	public function testGet() {
	}

}
