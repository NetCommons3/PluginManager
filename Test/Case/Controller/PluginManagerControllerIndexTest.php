<?php
/**
 * PluginManagerControllerIndexTest
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * PluginManagerControllerIndexTest
 *
 */
class PluginManagerControllerIndexTest extends NetCommonsControllerTestCase {

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
 * type data provider
 *
 * @return array
 */
	public function typeProvider() {
		return [
			[null],
			[1],
			[2],
			[3],
			[4],
			[5],
		];
	}

/**
 * testNoPluginData
 *
 * @param int $type Plugin.type
 * @dataProvider typeProvider
 * @return void
 */
	public function testNgClickValue($type) {
		$this->controller->Plugin = $this->getMockForModel('PluginManager.Plugin', ['getPlugins']);
		$value = [
			'Plugin' => [
				'id' => '2',
				'language_id' => '2',
				'is_origin' => true,
				'is_translation' => false,
				'is_original_copy' => false,
				'key' => 'plugin_manager',
				'is_m17n' => true,
				'name' => 'Lorem ipsum dolor sit amet',
				'namespace' => 'Lorem ipsum dolor sit amet',
				'weight' => '1',
				'type' => '1',
				'version' => null,
				'commit_version' => null,
				'commited' => null,
				'default_action' => '',
				'default_setting_action' => '',
				'frame_add_action' => null,
				'display_topics' => false,
				'display_search' => false,
				'serialize_data' => array(),
				'created_user' => null,
				'created' => null,
				'modified_user' => null,
				'modified' => null,
				'package_url' => 'Lorem ipsum dolor sit amet'
			],
			'latest' => null
		];

		$count = 2;
		if ($type == Plugin::PLUGIN_TYPE_FOR_NOT_YET) {
			$count = 1;
		}
		$this->controller->Plugin
			->expects($this->exactly($count))
			->method('getPlugins')
			->will($this->returnValue($value));

		$needle = '<a href="" ng-click="showView(plugin.plugin.type, plugin.plugin.key)">';

		$view = $this->testAction('plugin_manager/plugin_manager/index/' . $type, ['return' => 'view']);

		if ($type == Plugin::PLUGIN_TYPE_FOR_SYSTEM_MANGER) {
			$this->assertNotContains($needle, $view);
			return;
		}

		$this->assertContains($needle, $view);
	}

}
