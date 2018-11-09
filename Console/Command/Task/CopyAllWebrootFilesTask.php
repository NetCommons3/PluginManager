<?php
/**
 * UpdateAllTask
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppShell', 'Console/Command');
App::uses('PluginUpdateUtil', 'PluginManager.Utility');

/**
 * webrootファイルの一括コピーシェル
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Console\Command
 */
class CopyAllWebrootFilesTask extends AppShell {

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		$this->Plugin = ClassRegistry::init('PluginManager.Plugin');

		if (! isset($this->PluginUpdateUtil)) {
			$this->PluginUpdateUtil = new PluginUpdateUtil();
		}
		if ($this->PluginUpdateUtil->copyAllWebrootFiles()) {
			$this->out(
				'<success>' . __d('plugin_manager', 'Successfully webroot files copy of all plugins.') . '</success>'
			);
		} else {
			$this->out(
				'<error>' . __d('plugin_manager', 'Failure webroot files copy of all plugins.') . '</error>'
			);
		}
	}

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(__d('plugin_manager', 'Webroot files copy of all plugins'));

		return $parser;
	}
}
