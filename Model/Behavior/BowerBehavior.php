<?php
/**
 * Composer Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');
App::uses('File', 'Utility');

/**
 * Composer Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model\Behavior
 */
class BowerBehavior extends ModelBehavior {

/**
 * Bower update
 *
 * @param Model $model Model using this behavior
 * @param string $package Plugin namespace
 * @param string $option It is '' or '--save'. '--save' is used install.
 * @return bool True on success
 */
	public function updateBower(Model $model, $plugin, $option = '') {
		if (! $plugin) {
			return false;
		}

		$pluginPath = ROOT . DS . 'app' . DS . 'Plugin' . DS . Inflector::camelize($plugin) . DS;
		if (! file_exists($pluginPath . 'bower.json')) {
			return true;
		}

		$file = new File($pluginPath . 'bower.json');
		$bower = json_decode($file->read(), true);
		$file->close();

		foreach ($bower['dependencies'] as $package => $version) {
			CakeLog::info(sprintf('[bower] Start bower install %s#%s for %s', $package, $version, $plugin));

			$messages = array();
			$ret = null;
			exec(sprintf(
				'cd %s && `which bower` --allow-root install %s#%s %s',
				ROOT, $package, $version, $option
			), $messages, $ret);

			// Write logs
			if (Configure::read('debug')) {
				foreach ($messages as $message) {
					CakeLog::info(sprintf('[bower]   %s', $message));
				}
			}

			CakeLog::info(sprintf('[bower] Successfully bower install %s#%s for %s', $package, $version, $plugin));
		}

		return true;
	}

}
