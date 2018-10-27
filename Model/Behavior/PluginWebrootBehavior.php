<?php
/**
 * Plugin Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');
App::uses('Folder', 'Utility');

/**
 * Plugin Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model\Behavior
 */
class PluginWebrootBehavior extends ModelBehavior {

/**
 * 各プラグインにあるapp/webroot/img(css,js)にコピーする
 *
 * @param Model $model 呼び出し元Model
 * @param array $plugin プラグイン情報
 * @return bool
 */
	public function copyToWebroot(Model $model, $plugin) {
		$pluginKey = Hash::get($plugin, 'Plugin.key');
		if (! $pluginKey) {
			return true;
		}

		//既存のapp/webroot/img(css,js)を削除する
		$this->deleteFromWebroot($model, $plugin);
		$camelPlugin = Inflector::camelize($pluginKey);
		$originalSource = Hash::get($plugin, 'latest.originalSource');

		if (CakePlugin::loaded($camelPlugin)) {
			$pluginWebrootPath = CakePlugin::path($camelPlugin);
		} elseif (file_exists(APP . 'View' . DS . 'Themed' . DS . $originalSource)) {
			$pluginWebrootPath = APP . 'View' . DS . 'Themed' . DS . $originalSource . DS;
		} else {
			return true;
		}
		$pluginWebrootPath .= WEBROOT_DIR . DS;

		if (file_exists($pluginWebrootPath . 'img')) {
			$Folder = new Folder($pluginWebrootPath . 'img');
			$Folder->copy(IMAGES . DS . $pluginKey);
		}

		if (file_exists($pluginWebrootPath . 'css')) {
			$Folder = new Folder($pluginWebrootPath . 'css');
			$Folder->copy(CSS . DS . $pluginKey);
		}

		if (file_exists($pluginWebrootPath . 'js')) {
			$Folder = new Folder($pluginWebrootPath . 'js');
			$Folder->copy(JS . DS . $pluginKey);
		}

		return true;
	}

/**
 * app/webroot/img(css,js)から削除する
 *
 * @param Model $model 呼び出し元Model
 * @param array $plugin プラグイン情報
 * @return bool
 */
	public function deleteFromWebroot(Model $model, $plugin) {
		$pluginKey = Hash::get($plugin, 'Plugin.key');
		if (! $pluginKey) {
			return true;
		}

		if (file_exists(IMAGES . $pluginKey)) {
			$Folder = new Folder(IMAGES . $pluginKey);
			$Folder->delete();
		}

		if (file_exists(CSS . $pluginKey)) {
			$Folder = new Folder(CSS . $pluginKey);
			$Folder->delete();
		}

		if (file_exists(JS . $pluginKey)) {
			$Folder = new Folder(JS . $pluginKey);
			$Folder->delete();
		}

		return true;
	}

}
