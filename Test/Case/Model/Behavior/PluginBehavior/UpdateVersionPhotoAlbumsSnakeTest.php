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

App::uses('UpdateVersionPhotoAlbumsTest', 'PluginManager.Test/Case/Model/Behavior/PluginBehavior');

/**
 * PluginBehavior::updateVersion()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model\Behavior\PluginBehavior
 */
class UpdateVersionPhotoAlbumsSnakeTest extends UpdateVersionPhotoAlbumsTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.plugin_manager.plugin4photo_albums_snake',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'plugin_manager';

/**
 * updateVersion()のテスト
 *
 * @return void
 */
	public function testUpdateVersion() {
		//事前チェック
		$this->Plugin = ClassRegistry::init('PluginManager.Plugin');
		$count = $this->Plugin->find('count', array(
			'recursive' => -1,
			'conditions' => array('namespace' => 'netcommons/photo_albums'),
		));
		$this->assertEquals(2, $count);

		parent::testUpdateVersion();
	}

}
