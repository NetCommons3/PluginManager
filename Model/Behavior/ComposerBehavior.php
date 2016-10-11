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
		if (! $namespace) {
			return $composers;
		}

		$ret = Hash::extract($composers['packages'], '{n}[name=' . $namespace . ']');
		if ($ret) {
			return $ret[0];
		}
		$ret = Hash::extract($composers['packages-dev'], '{n}[name=' . $namespace . ']');
		if ($ret) {
			return $ret[0];
		}
		return null;
	}

}
