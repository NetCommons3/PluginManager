<?php
/**
 * PluginManagerのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

//@codeCoverageIgnoreStart;
App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
//@codeCoverageIgnoreEnd;

/**
 * PluginManagerのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\TestSuite
 * @codeCoverageIgnore
 */
class PluginManagerModelTestCase extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	protected $_fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'plugin_manager';

/**
 * Fixtures load
 *
 * @param string $name The name parameter on PHPUnit_Framework_TestCase::__construct()
 * @param array  $data The data parameter on PHPUnit_Framework_TestCase::__construct()
 * @param string $dataName The dataName parameter on PHPUnit_Framework_TestCase::__construct()
 * @return void
 * @codeCoverageIgnore
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		if (! isset($this->fixtures)) {
			$this->fixtures = array();
		}
		$this->fixtures = array_merge($this->_fixtures, $this->fixtures);
		parent::__construct($name, $data, $dataName);
	}

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'PluginManager', 'TestPluginManager');
		CakeLog::config('TestMockLog', array('engine' => 'TestPluginManager.TestMock'));
	}

}
