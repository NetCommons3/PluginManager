<?php
/**
 * PluginUpdate Utility
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * PluginUpdate Utility
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Utility
 */
class PluginUpdateUtil {

/**
 * コンストラクタ
 *
 * @return void
 */
	public function __construct() {
		$this->Plugin = ClassRegistry::init('PluginManager.Plugin');
	}

/**
 * 一括アップデート
 *
 * @return bool
 */
	public function updateAll() {
		$result = true;
		$types = $this->Plugin->getTypes();
		foreach ($types as $type) {
			$plugins = $this->Plugin->getPlugins($type, null, ['0', '2']);
			if (! $this->__updatePackages($plugins)) {
				$result = false;
				break;
			}
		}

		if ($result) {
			$types = array(
				Plugin::PLUGIN_TYPE_FOR_NOT_YET,
				Plugin::PLUGIN_TYPE_FOR_THEME,
				Plugin::PLUGIN_TYPE_FOR_EXT_COMPOSER,
				Plugin::PLUGIN_TYPE_FOR_EXT_BOWER
			);
			foreach ($types as $type) {
				$plugins = $this->Plugin->getNewPlugins($type);
				if (! $this->__updatePackages($plugins)) {
					$result = false;
					break;
				}
			}
		}

		return $result;
	}

/**
 * パッケージのバージョンアップ(一括アップデートから呼ばれる)
 *
 * @param array $plugins Pluginリスト
 * @return bool
 */
	private function __updatePackages($plugins) {
		if (! $plugins) {
			return true;
		}

		foreach ($plugins as $plugin) {
			if (! $this->Plugin->runVersionUp($plugin)) {
				return false;
			}
		}

		return true;
	}

/**
 * 一括アップデート
 *
 * @return bool
 */
	public function copyAllWebrootFiles() {
		$types = $this->Plugin->getTypes();
		foreach ($types as $type) {
			$plugins = $this->Plugin->getPlugins($type, null, ['0', '2']);
			foreach ($plugins as $plugin) {
				$this->Plugin->copyToWebroot($plugin);
			}
		}
		return true;
	}

}
