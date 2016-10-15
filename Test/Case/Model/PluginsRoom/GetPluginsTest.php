<?php
/**
 * PluginsRoom::getPlugins()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');

/**
 * PluginsRoom::getPlugins()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model\PluginsRoom
 */
class PluginsRoomGetPluginsTest extends NetCommonsGetTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.plugin_manager.plugin',
		'plugin.plugin_manager.plugins_role',
		'plugin.plugin_manager.plugins_room',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'plugin_manager';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'PluginsRoom';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getPlugins';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		Current::write('Language.id', '2');
	}

/**
 * getPlugins()のテスト
 *
 * @return void
 */
	public function testGetPlugins() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$roomId = '1';

		//テスト実施
		$result = $this->$model->$methodName($roomId);

		//チェック
		$this->assertCount(1, $result);

		$expected = array(
			'PluginsRoom', 'Plugin', 'Room'
		);
		$this->assertEquals($expected, array_keys($result[0]));
	}

/**
 * testGetPluginsError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderGetPluginsRoomIdError() {
		$checks = array(
			array('roomId' => null),
			array('roomId' => 0),
			array('roomId' => 'aaaa'),
		);

		return $checks;
	}

/**
 * testGetPluginsError
 *
 * @param mixed $roomId ルームID
 * @dataProvider dataProviderGetPluginsRoomIdError
 */
	public function testGetPluginsRoomIdError($roomId) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//テスト実施
		$result = $this->$model->$methodName($roomId);

		$this->assertFalse($result);
	}

}
