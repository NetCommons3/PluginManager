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
class AddVersionData extends NetCommonsMigration {

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
		),
		'down' => array(
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
		$this->loadModels([
			'Plugin' => 'PluginManager.Plugin',
		]);

		if (Configure::read('NetCommons.installed') && $this->Plugin->useDbConfig !== 'test') {
			if ($direction === 'up') {
				$conditions = array(
					'Plugin.key' => 'photo_albums'
				);
				$update = array(
					'Plugin.namespace' => '\'netcommons/photo-albums\''
				);
				if (! $this->Plugin->updateAll($update, $conditions)) {
					return false;
				}

				$filePath = App::pluginPath('PluginManager');
				$filePath .= 'Config' . DS . 'Migration' . DS . '1476173664_composer.lock';
				if (! $this->Plugin->updateVersionByComposer($filePath)) {
					return false;
				}

				$filePath = App::pluginPath('PluginManager');
				$filePath .= 'Config' . DS . 'Migration' . DS . '1476173664_bower.json';
				$file = new File($filePath);
				$contents = $file->read();
				$file->close();
				$packages = json_decode($contents, true);
				if (! $this->Plugin->updateVersionByBower($packages)) {
					return false;
				}

				if (! $this->Plugin->updateVersionByTheme()) {
					return false;
				}
			} else {
				$conditions = array('type' => array(
					Plugin::PLUGIN_TYPE_CORE,
					Plugin::PLUGIN_TYPE_FOR_THEME,
					Plugin::PLUGIN_TYPE_FOR_EXT_COMPOSER,
					Plugin::PLUGIN_TYPE_FOR_EXT_BOWER
				));
				if (! $this->Plugin->deleteAll($conditions, false, false)) {
					return false;
				}
			}
		}
		return true;
	}

}
