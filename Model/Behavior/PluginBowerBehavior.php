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
class PluginBowerBehavior extends ModelBehavior {

/**
 * Bowersのファイル取得
 *
 * @var array
 */
	public $bowers = array();

/**
 * bowerの情報取得
 *
 * @param Model $model 呼び出し元Model
 * @param string $namespace Pluginのnamespace
 * @param string $dirPath bowerのディレクトリ
 * @return mixed array|bool
 */
	public function getBower(Model $model, $namespace = null, $dirPath = null) {
		if ($this->bowers && ! $dirPath) {
			$bowers = $this->bowers;
		} elseif (is_array($dirPath)) {
			$bowers = array();
			foreach ($dirPath as $package) {
				$bower = $this->_parseBower($package, null);
				$bowers[$bower['namespace']] = $bower;
			}
		} else {
			$bowers = $this->__getBowerByFolder($dirPath);
		}

		if (! $namespace) {
			return $bowers;
		}

		return Hash::get($bowers, array($namespace));
	}

/**
 * bowerの情報取得
 *
 * @param string $dirPath bowerのディレクトリ
 * @return mixed array|bool
 */
	private function __getBowerByFolder($dirPath = null) {
		if ($dirPath) {
			$Folder = new Folder($dirPath);
		} else {
			$Folder = new Folder(WWW_ROOT . 'components');
		}
		$dirs = $Folder->read(Folder::SORT_NAME, false, true)[0];

		$bowers = array();
		foreach ($dirs as $dir) {
			$file = new File($dir . DS . '.bower.json');
			$contents = $file->read();
			$file->close();
			$package = json_decode($contents, true);

			$bower = $this->_parseBower($package, $dir);
			if (! Hash::get($bowers, array($bower['namespace'])) ||
					version_compare($bower['version'], $bowers[$bower['namespace']]['version']) > 0) {
				$bowers[$bower['namespace']] = $bower;
			}
		}

		if (! $dirPath) {
			$this->bowers = $bowers;
		}

		return $bowers;
	}

/**
 * bowerの情報をパースする
 *
 * @param array $package jsonファイルの情報
 * @param string $dir ディレクトリパス
 * @return mixed array
 */
	protected function _parseBower($package, $dir) {
		$result = array(
			'name' => Hash::get($package, 'name'),
			'key' => strtr(Hash::get($package, 'name'), '.', '-'),
			'type' => Plugin::PLUGIN_TYPE_FOR_EXT_BOWER,
			'description' => Hash::get($package, 'description'),
			'homepage' => Hash::get($package, 'homepage'),
			'version' => Hash::get($package, 'version'),
			'commit_version' => Hash::get($package, '_resolution.commit'),
			'source' => Hash::get($package, '_source', ''),
			'authors' => Hash::get($package, 'authors'),
			'license' => Hash::get($package, 'license'),
			'packageType' => 'bower',
			'originalSource' => Hash::get($package, '_originalSource', Hash::get($package, 'name')),
		);

		if (! $result['version']) {
			$result['version'] = Hash::get($package, '_release');
		}
		$pattern = '/^' . preg_quote('https://github.com/', '/') . '|\.git$/';
		$result['namespace'] = preg_replace($pattern, '', $result['source']);

		if ($dir && file_exists($dir . DS . 'bower.json')) {
			$result['commited'] = date('Y-m-d H:i:s', filemtime($dir . DS . 'bower.json'));
		} else {
			$result['commited'] = null;
		}

		if (preg_match('/^' . preg_quote(Plugin::GITHUB_URL, '/') . '/', $result['source']) &&
				Hash::get($result, 'commit_version')) {
			$result['commit_url'] = preg_replace('/\.git$/', '', $result['source']);
			$result['commit_url'] .= '/tree/' . Hash::get($result, 'commit_version');
		} else {
			$result['commit_url'] = null;
		}
		return $result;
	}

/**
 * bower.jsonファイルからバージョン情報をDBに更新する
 *
 * @param Model $model Model using this behavior
 * @param string $dirPath bowerのディレクトリ
 * @return bool
 */
	public function updateVersionByBower(Model $model, $dirPath = null) {
		$model->loadModels([
			'Plugin' => 'PluginManager.Plugin',
		]);
		$bowers = $this->getBower($model, null, $dirPath);
		$model->Plugin->updateVersion($bowers);
		return true;
	}

}
