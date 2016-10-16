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
class UpdateVersionPhotoAlbumsTest extends PluginManagerModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.plugin_manager.plugin4photo_albums',
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
 * updateVersion()のテスト
 *
 * @return void
 */
	public function testUpdateVersion() {
		//事前準備
		$namespace = 'netcommons/photo-albums';
		$filePath = App::pluginPath('PluginManager');
		$filePath .= 'Config' . DS . 'Migration' . DS . '1476173664_composer.lock';
		$package = $this->TestModel->getComposer($namespace, $filePath);

		//テスト実施
		$this->TestModel->Plugin = $this->getMockForModel(
			'PluginManager.Plugin', array('updateAll'), array('plugin' => 'PluginManager')
		);
		$expectedUpdate = array(
			'version' => '\'' . $package['version'] . '\'',
			'commit_version' => '\'' . $package['commit_version'] . '\'',
			'commited' => '\'' . $package['commited'] . '\'',
			'serialize_data' => '\'' . serialize($package) . '\'',
		);
		$expectedConditions = array(
			'namespace' => array('netcommons/photo-albums', 'netcommons/photo_albums')
		);
		$this->TestModel->Plugin->expects($this->once())
				->method('updateAll')
				->with($expectedUpdate, $expectedConditions)
				->will($this->returnValue(true));

		$result = $this->TestModel->updateVersion([$package]);
		$logger = CakeLog::stream('TestMockLog');

		//チェック
		$this->assertTrue($result);

		$expected = array(
			0 => 'Info: [update version] Start updated version data "netcommons/photo-albums"',
			1 => 'Info: [update version] Successfully updated "netcommons/photo-albums"',
		);
		$this->assertEquals($expected, $logger->output);
	}

}
