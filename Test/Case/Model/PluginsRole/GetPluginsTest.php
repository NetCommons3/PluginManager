<?php
/**
 * PluginsRole::getPlugins()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('CurrentLib', 'NetCommons.Lib');

/**
 * PluginsRole::getPlugins()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model\PluginsRole
 */
class PluginsRoleGetPluginsTest extends NetCommonsGetTest {

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
	protected $_modelName = 'PluginsRole';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getPlugins';

/**
 * getPlugins()のテスト
 *
 * @return void
 */
	public function testGetPlugins() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		CurrentLib::write('Language.id', '2');
		$pluginType = '1';
		$roleKey = 'system_administrator';
		$joinType = 'LEFT';

		//テスト実施
		$result = $this->$model->$methodName($pluginType, $roleKey, $joinType);

		//チェック
		$this->assertCount(1, $result);

		$expected = array(
			'PluginsRole', 'Plugin'
		);
		$this->assertEquals($expected, array_keys($result[0]));
	}

/**
 * testGetPluginsError
 *
 * @return void
 */
	public function testGetPluginsError() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$roleKey = false;
		$pluginType = '2';
		$joinType = 'LEFT';

		//テスト実施
		$result = $this->$model->$methodName($pluginType, $roleKey, $joinType);

		$this->assertFalse($result);
	}

}
