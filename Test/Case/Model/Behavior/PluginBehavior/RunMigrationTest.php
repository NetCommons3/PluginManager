<?php
/**
 * PluginBehavior::runMigration()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginManagerModelTestCase', 'PluginManager.TestSuite');

/**
 * PluginBehavior::runMigration()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model\Behavior\PluginBehavior
 */
class PluginBehaviorRunMigrationTest extends PluginManagerModelTestCase {

/**
 * Fixture merge
 *
 * @var array
 */
	protected $_isFixtureMerged = false;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.m17n.language',
		'plugin.migrations.schema_migrations',
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
 * テーブルクリア
 *
 * @param string $plugin Plugin key
 * @param array $dropTables 初期化するためにDrop Tableするリスト
 * @return void
 */
	private function __databaseClear($plugin, $dropTables) {
		$this->TestModel->query(
			'DELETE FROM ' . $this->TestModel->tablePrefix . 'schema_migrations ' .
			'WHERE type = \'' . Inflector::camelize($plugin) . '\''
		);

		foreach ($dropTables as $table) {
			$this->TestModel->query(
				'DROP TABLE IF EXISTS ' . $this->TestModel->tablePrefix . $table
			);
		}
	}

/**
 * runMigration()テストのDataProvider
 *
 * ### 戻り値
 *  - plugin Plugin key
 *  - dropTables 初期化するためにDrop Tableするリスト
 *
 * @return array データ
 */
	public function dataProvider() {
		return array(
			array('plugin' => 'plugin_manager', array('plugins', 'plugins_roles', 'plugins_rooms')),
			array('plugin' => 'PluginManager', array('plugins', 'plugins_roles', 'plugins_rooms')),
		);
	}

/**
 * Migrationありのテスト
 *
 * @param string $plugin Plugin key
 * @param array $dropTables 初期化するためにDrop Tableするリスト
 * @dataProvider dataProvider
 * @return void
 */
	public function testRunMigration($plugin, $dropTables) {
		//事前準備
		$this->__databaseClear($plugin, $dropTables);

		//テスト実施
		$result = $this->TestModel->runMigration($plugin);
		$logger = CakeLog::stream('TestMockLog');
		$output = implode('', $logger->output);

		//チェック
		$this->assertTrue($result);
		$this->assertTextNotContains('Failure', $output);

		$expected = 'Info: [migration] Start migrating "' . Inflector::camelize($plugin) . '" for test connection';
		$this->assertEquals($expected, $logger->output[0]);

		$expected = '[1434983278] 1434983278_init (2015-06-22 14:27:58)';
		$this->assertTextContains($expected, $output);

		$expected = '[1469523474] 1469523474_add_index (2016-07-26 08:57:54)';
		$this->assertTextContains($expected, $output);

		$expected = '[1469523475] 1469523475_plugin_records (2016-07-26 08:57:55)';
		$this->assertTextContains($expected, $output);

		$expected = '[1476173664] 1476173664_add_version_fields (2016-10-11 08:14:24)';
		$this->assertTextContains($expected, $output);

		$expected = 'Info: [migration] Successfully migrated "' . Inflector::camelize($plugin) . '" for test connection';
		$this->assertEquals($expected, $logger->output[count($logger->output) - 1]);
	}

/**
 * No migrationsのテスト
 *
 * @return void
 */
	public function testNoMigrations() {
		//事前準備
		$plugin = 'NetCommons';
		$dropTables = array();
		$this->__databaseClear($plugin, $dropTables);

		//テスト実施
		$result = $this->TestModel->runMigration($plugin);
		$logger = CakeLog::stream('TestMockLog');
		$output = implode('', $logger->output);

		//チェック
		$this->assertTrue($result);
		$this->assertTextNotContains('Failure', $output);

		$expected = 'Info: [migration] Start migrating "' . $plugin . '" for test connection';
		$this->assertEquals($expected, $logger->output[0]);

		$expected = 'Info: [migration]   No migrations available.';
		$this->assertTextContains($expected, $output);

		$expected = 'Info: [migration] Successfully migrated "' . $plugin . '" for test connection';
		$this->assertEquals($expected, $logger->output[count($logger->output) - 1]);
	}

/**
 * Migrationエラーのテスト
 *
 * @return void
 */
	public function testFailurePlugin() {
		//事前準備
		$plugin = 'TestPluginManager';
		$dropTables = array();
		$this->__databaseClear($plugin, $dropTables);

		//テスト実施
		$result = $this->TestModel->runMigration($plugin);
		$logger = CakeLog::stream('TestMockLog');
		$output = implode('', $logger->output);

		//チェック
		$this->assertFalse($result);
		$this->assertTextContains('Failure', $output);

		$expected = 'Info: [migration] Start migrating "' . $plugin . '" for test connection';
		$this->assertEquals($expected, $logger->output[0]);

		$expected = 'Info: [migration] Failure migrated "' . $plugin . '" for test connection';
		$this->assertEquals($expected, $logger->output[count($logger->output) - 1]);
	}

}
