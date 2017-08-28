<?php
/**
 * Plugin::getPlugins()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginManagerGetTest', 'PluginManager.TestSuite');

/**
 * Plugin::getPlugins()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model\Plugin
 */
class PluginGetPluginsTest extends PluginManagerGetTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'Plugin';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getPlugins';

/**
 * 単一プラグインのテスト
 *
 * @return void
 */
	public function testPlugin() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$type = '4';
		$key = 'plugin_manager';

		//テスト実施
		$result = $this->$model->$methodName($type, $key);

		//チェック
		$expected = $this->_getExpected();
		$this->assertEquals($expected, $result['Plugin']);
	}

/**
 * 複数プラグイン(key指定なし)のテスト
 *
 * @return void
 */
	public function testPluginsWOKey() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$type = '3';
		$key = null;

		//テスト実施
		$result = $this->$model->$methodName($type, $key);

		//チェック
		$this->assertTrue(3 === count($result));

		$expected = $this->_getExpected([
			'id' => '4',
			'language_id' => '2',
			'key' => 'user_manger',
			'name' => 'User Manager',
			'namespace' => 'netcommons/user-manager',
			'weight' => '1',
			'type' => '3',
			'package_url' => 'https://packagist.org/packages/netcommons/user-manager',
		]);
		$this->assertEquals($expected, $result[0]['Plugin']);

		$expected = $this->_getExpected([
			'id' => '5',
			'language_id' => '2',
			'key' => 'rooms',
			'name' => 'Room Manager',
			'namespace' => 'netcommons/rooms',
			'weight' => '2',
			'type' => '3',
			'package_url' => 'https://packagist.org/packages/netcommons/rooms',
		]);
		$this->assertEquals($expected, $result[1]['Plugin']);

		$expected = $this->_getExpected([
			'id' => '6',
			'language_id' => '2',
			'key' => 'user_roles',
			'name' => 'UserRole Manager',
			'namespace' => 'netcommons/user-roles',
			'weight' => '3',
			'type' => '3',
			'package_url' => 'https://packagist.org/packages/netcommons/user-roles',
		]);
		$this->assertEquals($expected, $result[2]['Plugin']);
	}

/**
 * 複数プラグイン(key指定あり)のテスト
 *
 * @return void
 */
	public function testPluginsWKey() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$type = '3';
		$key = ['user_manger', 'user_roles'];

		//テスト実施
		$result = $this->$model->$methodName($type, $key);

		//チェック
		$this->assertTrue(2 === count($result));

		$expected = $this->_getExpected([
			'id' => '4',
			'language_id' => '2',
			'key' => 'user_manger',
			'name' => 'User Manager',
			'namespace' => 'netcommons/user-manager',
			'weight' => '1',
			'type' => '3',
			'package_url' => 'https://packagist.org/packages/netcommons/user-manager',
		]);
		$this->assertEquals($expected, $result[0]['Plugin']);

		$expected = $this->_getExpected([
			'id' => '6',
			'language_id' => '2',
			'key' => 'user_roles',
			'name' => 'UserRole Manager',
			'namespace' => 'netcommons/user-roles',
			'weight' => '3',
			'type' => '3',
			'package_url' => 'https://packagist.org/packages/netcommons/user-roles',
		]);
		$this->assertEquals($expected, $result[1]['Plugin']);
	}

/**
 * 期待値の取得
 *
 * @param array $mergeExpected マージする期待値
 * @return array
 */
	protected function _getExpected($mergeExpected = []) {
		$expected = array_merge(
			[
				'id' => '2',
				'language_id' => '2',
				'is_origin' => true,
				'is_translation' => false,
				'is_original_copy' => false,
				'key' => 'plugin_manager',
				'is_m17n' => true,
				'name' => 'Plugin Manager',
				'namespace' => 'netcommons/plugin-manager',
				'weight' => '1',
				'type' => '4',
				'version' => null,
				'commit_version' => null,
				'commited' => null,
				'default_action' => '',
				'default_setting_action' => '',
				'frame_add_action' => null,
				'display_topics' => false,
				'display_search' => false,
				'serialize_data' => [
				],
				'created_user' => null,
				'created' => null,
				'modified_user' => null,
				'modified' => null,
				'package_url' => 'https://packagist.org/packages/netcommons/plugin-manager',
			],
			$mergeExpected
		);

		return $expected;
	}

}
