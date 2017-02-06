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
 * 一括アップデートシェル
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Console\Command
 */
class UpdateAllTask extends AppShell {

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		$this->Plugin = ClassRegistry::init('PluginManager.Plugin');
		if (! $this->Plugin->runMigration('plugin_manager')) {
			$this->out(
				'<error>' .
					__d('plugin_manager', 'Failure updated of "plugin_manager" plugin.') .
				'</error>'
			);
			return $this->_stop();
		}
		if (! $this->Plugin->runMigration('site_manager')) {
			$this->out(
				'<error>' .
					__d('plugin_manager', 'Failure updated of \"site_manager\" plugin.') .
				'</error>'
			);
			return $this->_stop();
		}

		if (! isset($this->PluginUpdateUtil)) {
			$this->PluginUpdateUtil = new PluginUpdateUtil();
		}
		if ($this->PluginUpdateUtil->updateAll()) {
			$this->out(
				'<success>' . __d('plugin_manager', 'Successfully updated of all plugins.') . '</success>'
			);
		} else {
			$this->out('<error>' . __d('plugin_manager', 'Failure updated of all plugins.') . '</error>');
		}
	}

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(__d('plugin_manager', 'Update of all plugins'));

		return $parser;
	}
}
