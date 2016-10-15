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
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('Plugin', 'PluginManager.Model');

/**
 * Plugin Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model\Behavior
 */
class PluginComposerBehavior extends ModelBehavior {

/**
 * Composersのファイル取得
 *
 * @var array
 */
	public $composers = array();

/**
 * composer.lockから情報取得
 *
 * @param Model $model 呼び出し元Model
 * @param string $namespace Pluginのnamespace
 * @param string|array $filePath ファイルパスもしくはcomposer.lockファイルデータ
 * @return mixed array|bool
 */
	public function getComposer(Model $model, $namespace = null, $filePath = null) {
		if ($this->composers && ! $filePath) {
			$composers = $this->composers;
		} else {
			if ($filePath) {
				$file = new File($filePath);
			} else {
				$file = new File(ROOT . DS . 'composer.lock');
			}
			$contents = $file->read();
			$file->close();

			$packages = json_decode($contents, true);

			$composers = array();
			foreach ($packages['packages'] as $package) {
				$composer = $this->_parseComposer($package);
				$composers[$composer['namespace']] = $composer;
			}

			if (! $filePath) {
				$this->composers = $composers;
			}
		}

		if (! $namespace) {
			return $composers;
		}

		return Hash::get($composers, array($namespace));
	}

/**
 * bowerの情報をパースする
 *
 * @param array $package jsonファイルの情報
 * @return mixed array
 */
	protected function _parseComposer($package) {
		if (preg_match('#^netcommons/#', $package['name'])) {
			$key = strtr(preg_replace('#^netcommons/#', '', $package['name']), '-', '_');
			$name = Inflector::humanize($key);
			$originalSource = Inflector::camelize($key);
		} elseif (Hash::get($package, 'type') === 'cakephp-plugin') {
			$key = strtr(substr($package['name'], strrpos($package['name'], '/') + 1), '-', '_');
			$name = Inflector::humanize(strtr($package['name'], '/', ' '));
			$originalSource = Inflector::camelize($key);
		} else {
			$key = $package['name'];
			$name = $package['name'];
			$originalSource = $package['name'];
		}

		$result = array(
			'key' => $key,
			'namespace' => Hash::get($package, 'name'),
			'description' => Hash::get($package, 'description'),
			'homepage' => Hash::get($package, 'homepage'),
			'version' => Hash::get($package, 'version'),
			'commit_version' => Hash::get($package, 'source.reference'),
			'source' => Hash::get($package, 'source.url', ''),
			'authors' => Hash::get($package, 'authors'),
			'license' => Hash::get($package, 'license'),
			'commited' => Hash::get($package, 'time'),
			'packageType' => Hash::get($package, 'type'),
			'originalSource' => $originalSource
		);
		if (isset($package['extra']['installer-name'])) {
			$result['key'] = Inflector::underscore($package['extra']['installer-name']);
			$result['name'] = $package['extra']['installer-name'];
			$result['originalSource'] = $package['extra']['installer-name'];
		} else {
			$result['name'] = $name;
		}
		if (isset($package['plugin-type'])) {
			$result['type'] = $package['plugin-type'];
		} elseif (! preg_match('#^netcommons/#', $package['name'])) {
			$result['type'] = Plugin::PLUGIN_TYPE_FOR_EXT_COMPOSER;
		}

		if (Hash::get($package, 'source.type') === 'git' &&
				$result['source'] && $result['commit_version']) {
			$result['commit_url'] = preg_replace('/\.git$/', '', $result['source']);
			$result['commit_url'] .= '/tree/' . $result['commit_version'];
		} else {
			$result['commit_url'] = null;
		}

		return $result;
	}

/**
 * composer.lockファイルからバージョン情報をDBに更新する
 *
 * @param Model $model 呼び出し元Model
 * @param string|array $filePath ファイルパスもしくはcomposer.lockファイルデータ
 * @return bool
 */
	public function updateVersionByComposer(Model $model, $filePath = null) {
		$model->loadModels([
			'Plugin' => 'PluginManager.Plugin',
		]);
		$composers = $this->getComposer($model, null, $filePath);
		$model->Plugin->updateVersion($composers);
		return true;
	}

}
