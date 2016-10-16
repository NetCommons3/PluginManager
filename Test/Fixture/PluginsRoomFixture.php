<?php
/**
 * PluginsRoomFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * PluginsRoomFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Fixture
 */
class PluginsRoomFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'room_id' => '1',
			'plugin_key' => 'plugin_manager'
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('PluginManager') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new PluginManagerSchema())->tables[Inflector::tableize($this->name)];
		parent::init();
	}

}
