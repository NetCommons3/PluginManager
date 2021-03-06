<?php
/**
 * versionフィールド追加 migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');
App::uses('Plugin', 'PluginManager.Model');
App::uses('File', 'Utility');

/**
 * versionフィールド追加 migration
 *
 * @package NetCommons\PluginManager\Config\Migration
 */
class AddVersionFields extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_version_fields';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'plugins' => array(
					'version' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バージョン', 'charset' => 'utf8', 'after' => 'type'),
					'commit_version' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コミットバージョン', 'charset' => 'utf8', 'after' => 'version'),
					'commited' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'commit_version'),
					'serialize_data' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'display_search'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'plugins' => array('version', 'commit_version', 'commited', 'serialize_data'),
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
