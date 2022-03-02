<?php
/**
 * PluginBehavior::updateVersion()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginManagerModelTestCase', 'PluginManager.TestSuite');

/**
 * PluginBehavior::updateVersion()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model\Behavior\PluginBehavior
 */
class PluginBehaviorUpdateVersionTest extends PluginManagerModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.plugin_manager.plugin4test',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'plugin_manager';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->TestModel = ClassRegistry::init('TestPluginManager.TestPluginBehaviorModel');
	}

/**
 * テスト事前準備
 *
 * @param string $namespace namespace
 * @param bool $return 戻り値
 * @return array データ
 */
	private function __prepareUpdateAll($namespace, $return) {
		//事前準備
		$package = $this->TestModel->getComposer($namespace);

		$this->TestModel->Plugin = $this->getMockForModel(
			'PluginManager.Plugin', array('updateAll'), array('plugin' => 'PluginManager')
		);
		$expectedUpdate = array(
			'version' => '\'' . $package['version'] . '\'',
			'commit_version' => '\'' . $package['commit_version'] . '\'',
			'commited' => '\'' . $package['commited'] . '\'',
			'serialize_data' => $this->TestModel->Plugin->getDataSource()->value(serialize($package), 'string'),
		);
		$expectedConditions = array(
			'namespace' => $namespace
		);
		$this->TestModel->Plugin->expects($this->once())
				->method('updateAll')
				->with($expectedUpdate, $expectedConditions)
				->will($this->returnValue($return));

		return $package;
	}

/**
 * テスト事前準備
 *
 * @param string $namespace namespace
 * @param int $type プラグインType
 * @param bool $return 戻り値
 * @return array データ
 */
	private function __prepareSave($namespace, $type, $return) {
		//事前準備
		$package = $this->TestModel->getComposer($namespace);

		$this->TestModel->Plugin = $this->getMockForModel(
			'PluginManager.Plugin', array('save'), array('plugin' => 'PluginManager')
		);
		$expected = array(
			'language_id' => '0',
			'name' => $package['name'],
			'key' => $package['key'],
			'namespace' => $package['namespace'],
			'type' => $type,
			'version' => $package['version'],
			'commit_version' => $package['commit_version'],
			'commited' => $package['commited'],
			'serialize_data' => serialize($package),
			'is_m17n' => null,
		);
		$this->TestModel->Plugin->expects($this->once())
				->method('save')
				->with($expected)
				->will($this->returnValue($return));

		return $package;
	}

/**
 * 既にある場合のテスト
 *
 * @return void
 */
	public function testExistsData() {
		//事前準備
		$namespace = 'netcommons/plugin-manager';
		$package = $this->__prepareUpdateAll($namespace, true);

		//テスト実施
		$result = $this->TestModel->updateVersion([$package]);

		//チェック
		$this->assertTrue($result);
		$logger = CakeLog::stream('TestMockLog');

		$expected = array(
			0 => 'Info: [update version] Start updated version data "' . $namespace . '"',
			1 => 'Info: [update version] Successfully updated "' . $namespace . '"',
		);
		$this->assertEquals($expected, $logger->output);
	}

/**
 * Plugin->updateAll()エラーのテスト
 *
 * @return void
 */
	public function testUpdateAllError() {
		//事前準備
		$namespace = 'netcommons/plugin-manager';
		$package = $this->__prepareUpdateAll($namespace, false);

		//テスト実施
		try {
			$this->TestModel->updateVersion([$package]);
		} catch (Exception $ex) {
			$logger = CakeLog::stream('TestMockLog');
		}
		$this->assertEquals(__d('net_commons', 'Internal Server Error'), $ex->getMessage());

		$expected = array(
			0 => 'Info: [update version] Start updated version data "netcommons/plugin-manager"',
			4 => 'Info: [update version] Failure updated "netcommons/plugin-manager"',
		);
		$this->assertEquals($expected[0], $logger->output[0]);
		$this->assertEquals($expected[4], $logger->output[4]);
	}

/**
 * 存在しない場合のテスト用DataProvider
 *
 * ### 戻り値
 *  - namespace namespace
 *  - type プラグインType
 *
 * @return array データ
 */
	public function dataProvider() {
		return array(
			array('namespace' => 'netcommons/control-panel', 'type' => '0'),
			array('namespace' => 'cakephp/cakephp', 'type' => '6'),
		);
	}

/**
 * 存在しない場合のテスト
 *
 * @param string $namespace namespace
 * @param int $type プラグインType
 * @dataProvider dataProvider
 * @return void
 */
	public function testNotExistsData($namespace, $type) {
		//事前準備
		$package = $this->__prepareSave($namespace, $type, true);

		//テスト実施
		$result = $this->TestModel->updateVersion([$package]);

		//チェック
		$this->assertTrue($result);
		$logger = CakeLog::stream('TestMockLog');

		$expected = array(
			0 => 'Info: [update version] Start updated version data "' . $namespace . '"',
			1 => 'Info: [update version] Successfully updated "' . $namespace . '"',
		);
		$this->assertEquals($expected, $logger->output);
	}

/**
 * Plugin->save()エラーのテスト
 *
 * @return void
 */
	public function testSaveError() {
		//事前準備
		$namespace = 'netcommons/control-panel';
		$package = $this->__prepareSave($namespace, '0', false);

		//テスト実施
		try {
			$this->TestModel->updateVersion([$package]);
		} catch (Exception $ex) {
			$logger = CakeLog::stream('TestMockLog');
		}
		$this->assertEquals(__d('net_commons', 'Internal Server Error'), $ex->getMessage());

		$expected = array(
			0 => 'Info: [update version] Start updated version data "netcommons/control-panel"',
			3 => 'Info: [update version] Failure updated "netcommons/control-panel"',
		);
		$this->assertEquals($expected[0], $logger->output[0]);
		$this->assertEquals($expected[3], $logger->output[3]);
	}

}
