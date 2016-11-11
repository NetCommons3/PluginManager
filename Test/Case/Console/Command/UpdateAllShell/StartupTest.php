<?php
/**
 * UpdateAllShell::startup()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginManagerConsoleTestCase', 'PluginManager.TestSuite');

/**
 * UpdateAllShell::startup()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Console\Command\UpdateAllShell
 */
class PluginManagerConsoleCommandUpdateAllShellStartupTest extends PluginManagerConsoleTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'plugin_manager';

/**
 * Shell name
 *
 * @var string
 */
	protected $_shellName = 'UpdateAllShell';

/**
 * startup()のテスト
 *
 * @return void
 */
	public function testStartup() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadShell($shell);

		//チェック
		$this->$shell->expects($this->at(1))->method('out')
			->with(__d('plugin_manager', 'Update of all plugins'));

		//テスト実施
		$this->$shell->startup();
	}

}
