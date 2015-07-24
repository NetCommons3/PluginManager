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
class ComposerBehavior extends ModelBehavior {

/**
 * Get plugin information data from composer.lock
 *
 * @param Model $model Model using this behavior
 * @param string $namespace Plugin namespace
 * @return mixed array|bool
 */
	public function getComposer(Model $model, $namespace = null) {
		static $composers = null;

		if (! $composers) {
			$filePath = ROOT . DS . 'composer.lock';
			$file = new File($filePath);
			$contents = $file->read();
			$file->close();

			$composers = json_decode($contents, true);
		}
		$ret = Hash::extract($composers['packages-dev'], '{n}[name=' . $namespace . ']');
		if ($ret) {
			return $ret[0];
		} else {
			return null;
		}
	}

/**
 * Composer update
 *
 * @param Model $model Model using this behavior
 * @param string $package Plugin namespace
 * @param string $option It is 'update' or 'require --dev'. 'require --dev' is used install.
 * @return bool True on success
 */
	public function updateComposer(Model $model, $package, $option = 'update') {
		static $hhvm = null;

		if (! $package) {
			return false;
		}

		if (! isset($hhvm)) {
			// Use hhvm only if php version greater than 5.5.0 and hhvm installed
			// @see https://github.com/facebook/hhvm/wiki/OSS-PHP-Frameworks-Unit-Testing
			$gt55 = version_compare(phpversion(), '5.5.0', '>=');
			exec('which hhvm', $messages, $ret);
			$hhvm = ($gt55 && $ret === 0) ? 'hhvm -vRepo.Central.Path=/var/run/hhvm/hhvm.hhbc' : '';
		}

		CakeLog::info(sprintf('[composer] Start composer %s %s', $option, $package));

		$messages = array();
		$ret = null;
		$cmd = sprintf(
			'export COMPOSER_HOME=%s && cd %s && %s `which composer` %s %s 2>&1',
			ROOT, ROOT, $hhvm, $option, $package
		);
		exec($cmd, $messages, $ret);

		// Write logs
		if (Configure::read('debug') || $ret !== 0) {
			foreach ($messages as $message) {
				CakeLog::info(sprintf('[composer]   %s', $message));
			}
		}
		if ($ret !== 0) {
			return false;
		}

		CakeLog::info(sprintf('[composer] Successfully composer %s %s', $option, $package));

		return true;
	}

}
