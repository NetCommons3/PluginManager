<?php
/**
 * PluginManagerControllerEditTest
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * PluginManagerControllerEditTest
 *
 */
class PluginManagerControllerEditTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [];

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'plugin_manager';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'plugin_manager';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		TestAuthGeneral::login($this);

		// PluginsRole.plugin_key が 'test_plugin' しかないので Permission denied になる
		// fixture に追加でも良かったが、とりあえずMockで対応
		// @see https://github.com/NetCommons3/NetCommons/blob/3.1.3/Utility/Current.php#L450
		$this->controller = $this->generate('PluginManager.PluginManager');
		$MockPermission = $this->getMock('PermissionComponent', ['startup'], [$this->controller->Components]);
		$MockPermission
			->expects($this->once())
			->method('startup')
			->will($this->returnValue(true));
		$this->controller->Components->set('Permission', $MockPermission);
		$this->controller->Components->enable('Permission');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		TestAuthGeneral::logout($this);
		parent::tearDown();
	}

/**
 * testGetAction
 *
 * @expectedException BadRequestException
 * @return void
 */
	public function testGetAction() {
		$this->testAction('plugin_manager/plugin_manager/edit', ['method' => 'GET']);
	}

/**
 * testNoPluginData
 *
 * @expectedException BadRequestException
 * @return void
 */
	public function testNoPluginData() {
		$data['Plugin']['key'] = 'dummy';
		$this->testAction('plugin_manager/plugin_manager/edit/99', ['data' => $data]);
	}

/**
 * testRunVersionUpTrue
 *
 * @return void
 */
	public function testRunVersionUpTrue() {
		$this->__setMockPluginRunVersionUp(true);

		$MockNetCommons = $this->getMock('NetCommonsComponent', ['setFlashNotification'], [$this->controller->Components]);
		$MockNetCommons
			->expects($this->once())
			->method('setFlashNotification')
			->with(
				$this->anything(),
				['class' => 'success']
			);
		$this->controller->Components->set('NetCommons', $MockNetCommons);
		$this->controller->Components->enable('NetCommons');

		$data['Plugin']['key'] = 'plugin_manager';
		$this->testAction('plugin_manager/plugin_manager/edit/2', ['data' => $data]);
		$this->assertStringEndsWith('plugin_manager/plugin_manager/index/2', $this->headers['Location']);
	}

/**
 * testRunVersionUpFalse
 *
 * @return void
 */
	public function testRunVersionUpFalse() {
		$this->__setMockPluginRunVersionUp(false);

		$MockNetCommons = $this->getMock('NetCommonsComponent', ['setFlashNotification'], [$this->controller->Components]);
		$MockNetCommons
			->expects($this->once())
			->method('setFlashNotification')
			->with(
				$this->anything(),
				[
					'class' => 'danger',
					'interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL,
				]
			);
		$this->controller->Components->set('NetCommons', $MockNetCommons);
		$this->controller->Components->enable('NetCommons');

		$data['Plugin']['key'] = 'plugin_manager';
		$this->testAction('plugin_manager/plugin_manager/edit/2', ['data' => $data]);
		$this->assertStringEndsWith('plugin_manager/plugin_manager/index/2', $this->headers['Location']);
	}

/**
 * testTypeSystem
 *
 * @return void
 */
	public function testTypeSystem() {
		$this->__setMockPluginRunVersionUp(true);

		$data['Plugin']['key'] = 'plugin_manager';
		$this->testAction('plugin_manager/plugin_manager/edit/3', ['data' => $data]);
		$this->assertStringEndsWith('plugin_manager/plugin_manager/index/2', $this->headers['Location']);
	}

/**
 * testTypeNotYet
 *
 * @return void
 */
	public function testTypeNotYet() {
		$this->__setMockPluginRunVersionUp(true);

		$this->controller->Plugin
			->expects($this->exactly(2))
			->method('getNewPlugins')
			->will($this->onConsecutiveCalls(true, false));

		$data['Plugin']['key'] = 'plugin_manager';
		$this->testAction('plugin_manager/plugin_manager/edit/4', ['data' => $data]);

		// Routing でindexがなくなると思われる。
		//$this->assertStringEndsWith('plugin_manager/plugin_manager/index', $this->headers['Location']);
		$this->assertStringEndsWith('plugin_manager', $this->headers['Location']);
	}

/**
 * Set MockPluginRunVersionUp
 *
 * @param PHPUnit_Framework_MockObject_Stub_Return $returnValue Value returned by Mock
 * @return void
 */
	private function __setMockPluginRunVersionUp($returnValue) {
		$this->controller->Plugin = $this->getMockForModel('PluginManager.Plugin', ['runVersionUp', 'getNewPlugins']);
		$this->controller->Plugin
			->expects($this->once())
			->method('runVersionUp')
			->will($this->returnValue($returnValue));
	}

}
