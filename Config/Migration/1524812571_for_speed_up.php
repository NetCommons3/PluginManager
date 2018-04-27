<?php
/**
 * 速度改善のためのインデックス見直し
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 速度改善のためのインデックス見直し
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Config\Migration
 */
class ForSpeedUp extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'for_speed_up';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'plugins' => array(
					'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false, 'key' => 'index'),
					'weight' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '表示順序'),
					'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => 'プラグインタイプ 1:一般プラグイン,2:コントロールパネル/サイト管理プラグイン,3:システム管理プラグイン,4:未インストールのため抜け番,5:テーマ,6:外部ライブラリ composer,7:外部ライブラリ bower'),
				),
			),
			'create_field' => array(
				'plugins' => array(
					'indexes' => array(
						'language_id' => array('column' => 'language_id', 'unique' => 0),
						'weight' => array('column' => 'weight', 'unique' => 0),
						'types' => array('column' => array('type', 'language_id'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'plugins' => array(
					'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false),
					'weight' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '表示順序'),
					'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'プラグインタイプ 1:一般プラグイン,2:コントロールパネル/サイト管理プラグイン,3:システム管理プラグイン,4:未インストールのため抜け番,5:テーマ,6:外部ライブラリ composer,7:外部ライブラリ bower'),
				),
			),
			'drop_field' => array(
				'plugins' => array('indexes' => array('language_id', 'weight', 'types')),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
