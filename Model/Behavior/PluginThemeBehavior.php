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
App::uses('Security', 'Utility');

/**
 * Plugin Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model\Behavior
 */
class PluginThemeBehavior extends ModelBehavior {

/**
 * Themesのファイル取得
 *
 * @var array
 */
	public $themes = array();

/**
 * themeの情報取得
 *
 * @param Model $model 呼び出し元Model
 * @param string $namespace Pluginのnamespace
 * @param string $dirPath themeのディレクトリ
 * @return mixed array|bool
 */
	public function getTheme(Model $model, $namespace = null, $dirPath = null) {
		if ($this->themes && ! $dirPath) {
			$themes = $this->themes;
		} else {
			if ($dirPath) {
				$Folder = new Folder($dirPath);
			} else {
				$Folder = new Folder(APP . 'View' . DS . 'Themed');
			}
			$dirs = $Folder->read(Folder::SORT_NAME, false, true)[0];

			$themes = array();
			foreach ($dirs as $dir) {
				$file = new File($dir . DS . 'theme.json');
				$contents = $file->read();
				$file->close();
				$package = json_decode($contents, true);

				$theme = $this->_parseTheme($package, $dir);
				$themes[$theme['namespace']] = $theme;
			}

			if (! $dirPath) {
				$this->themes = $themes;
			}
		}

		if (! $namespace) {
			return $themes;
		}

		return Hash::get($themes, array($namespace));
	}

/**
 * bowerの情報をパースする
 *
 * @param array $package jsonファイルの情報
 * @param string $dir ディレクトリパス
 * @return mixed array
 */
	protected function _parseTheme($package, $dir) {
		$result = array(
			'name' => Hash::get($package, 'name'),
			'key' => Inflector::underscore(basename($dir)),
			'namespace' => basename($dir),
			'type' => Plugin::PLUGIN_TYPE_FOR_THEME,
			'description' => Hash::get($package, 'description'),
			'homepage' => Hash::get($package, 'homepage'),
			'version' => Hash::get($package, 'version'),
			'commit_version' => Hash::get($package, 'source.reference'),
			'source' => Hash::get($package, 'source.url', ''),
			'authors' => Hash::get($package, 'authors'),
			'license' => Hash::get($package, 'license'),
			'commited' => Hash::get($package, 'time'),
			'packageType' => Hash::get($package, 'type'),
			'originalSource' => basename($dir)
		);

		if (! $result['commit_version']) {
			$result['commit_version'] = Security::hash($result['namespace'] . $result['version'], 'md5');
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
 * theme.jsonファイルからバージョン情報をDBに更新する
 *
 * @param Model $model Model using this behavior
 * @param string $dirPath themeのディレクトリ
 * @return bool
 */
	public function updateVersionByTheme(Model $model, $dirPath = null) {
		$model->loadModels([
			'Plugin' => 'PluginManager.Plugin',
		]);
		$themes = $this->getTheme($model, null, $dirPath);
		$model->Plugin->updateVersion($themes);
		return true;
	}

}
