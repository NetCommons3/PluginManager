<?php
/**
 * Plugin::hasUpdate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * Plugin::hasUpdate()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case\Model\Plugin
 */
class PluginHasUpdateTest extends NetCommonsModelTestCase {

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
	protected $_modelName = 'Plugin';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'hasUpdate';

/**
 * hasUpdate()テストのDataProvider
 *
 * ### 戻り値
 *  - composer 最新のcomposerデータ
 *  - bower 最新のbowerデータ
 *  - themes 最新のthemeデータ
 *  - current pluginsテーブルに登録されているデータ
 *  - expected 期待値
 *
 * @return array データ
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function dataProvider() {
		$composers = array(
			'netcommons/net-commons' => array(
				'key' => 'netcommons',
				'version' => '3.0.0',
				'commit_version' => md5('netcommons 3.0.0'),
			),
			'cakephp/cakephp' => array(
				'key' => 'cakephp/cakephp',
				'version' => '2.8.9',
				'commit_version' => md5('cakephp 2.8.9'),
			),
			'netcommons/plugin-manager' => array(
				'key' => 'plugin_manager',
				'namespace' => 'netcommons/plugin-manager',
				'version' => '3.0.0',
				'commit_version' => md5('plugin_manager 3.0.0'),
			),
		);
		$bowers = array(
			'angular/bower-angular' => array(
				'key' => 'angular',
				'namespace' => 'angular/bower-angular',
				'version' => '1.5.8',
				'commit_version' => md5('angular 1.5.8'),
			),
			'tinymce/tinymce-dist' => array(
				'key' => 'tinymce',
				'namespace' => 'tinymce/tinymce-dist',
				'version' => '4.4.3',
				'commit_version' => md5('tinymce 4.4.3'),
			),
			'Eonasdan/bootstrap-datetimepicker' => array(
				'key' => 'eonasdan-bootstrap-datetimepicker',
				'namespace' => 'Eonasdan/bootstrap-datetimepicker',
				'version' => '4.17.43',
				'commit_version' => md5('eonasdan-bootstrap-datetimepicker 4.17.43'),
			),
		);
		$themes = array(
			'Themed/Default' => array(
				'key' => 'themed_default',
				'namespace' => 'Themed/Default',
				'version' => '3.0.0',
				'commit_version' => md5('themed_default 3.0.0'),
			),
			'Themed/DefaultPink' => array(
				'key' => 'themed_default_pink',
				'namespace' => 'Themed/DefaultPink',
				'version' => '3.0.0',
				'commit_version' => md5('themed_default_pink 3.0.0'),
			),
			'Themed/LayoutBlue' => array(
				'key' => 'themed_layout_blue',
				'namespace' => 'Themed/LayoutBlue',
				'version' => '3.0.0',
				'commit_version' => md5('themed_layout_blue 3.0.0'),
			),
		);
		$current = array_merge($composers, $bowers, $themes);
		$current = Hash::combine($current, '{s}.key', '{s}.commit_version');

		//0: 変更なし
		$index = 0;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => false,
		);

		//1: composer変更あり
		$index = 1;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => true,
		);
		$result[$index]['composers']['cakephp/cakephp']['version'] = '2.9.0';
		$result[$index]['composers']['cakephp/cakephp']['commit_version'] = md5('2.9.0');

		//2: bower変更あり
		$index = 2;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => true,
		);
		$result[$index]['bowers']['tinymce/tinymce-dist']['version'] = '4.4.4';
		$result[$index]['bowers']['tinymce/tinymce-dist']['commit_version'] = md5('4.4.4');

		//3: theme変更あり
		$index = 3;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => true,
		);
		$result[$index]['themes']['Themed/DefaultPink']['version'] = '3.0.1';
		$result[$index]['themes']['Themed/DefaultPink']['commit_version'] = md5('3.0.1');

		//4: composer新規あり
		$index = 4;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => true,
		);
		unset($result[$index]['current']['plugin_manager']);

		//5: bower新規あり
		$index = 5;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => true,
		);
		unset($result[$index]['current']['eonasdan-bootstrap-datetimepicker']);

		//6: theme新規あり
		$index = 6;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => true,
		);
		unset($result[$index]['current']['themed_layout_blue']);

		//7: composer削除
		$index = 7;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => true,
		);
		unset($result[$index]['composers']['netcommons/plugin-manager']);

		//8: bower削除
		$index = 8;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => true,
		);
		unset($result[$index]['bowers']['Eonasdan/bootstrap-datetimepicker']);

		//9: theme削除
		$index = 9;
		$result[$index] = array(
			'composers' => $composers, 'bowers' => $bowers, 'themes' => $themes, 'current' => $current,
			'expected' => true,
		);
		unset($result[$index]['themes']['Themed/LayoutBlue']);

		return $result;
	}

/**
 * hasUpdate()のテスト
 *
 * @param array $composers 最新のcomposerデータ
 * @param array $bowers 最新のbowerデータ
 * @param array $themes 最新のthemeデータ
 * @param array $current pluginsテーブルに登録されているデータ
 * @param bool $expected 期待値
 * @dataProvider dataProvider
 * @return void
 */
	public function testHasUpdate($composers, $bowers, $themes, $current, $expected) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$this->$model = $this->getMockForModel(
			'PluginManager.Plugin',
			array('getComposer', 'getBower', 'getTheme', 'find'),
			array('plugin' => 'PluginManager')
		);
		$this->$model->expects($this->once())->method('getComposer')->will($this->returnValue($composers));
		$this->$model->expects($this->once())->method('getBower')->will($this->returnValue($bowers));
		$this->$model->expects($this->once())->method('getTheme')->will($this->returnValue($themes));
		$this->$model->expects($this->once())->method('find')->will($this->returnValue($current));

		//テスト実施
		$result = $this->$model->$methodName();

		//チェック
		$this->assertEquals($expected, $result);
	}

}
