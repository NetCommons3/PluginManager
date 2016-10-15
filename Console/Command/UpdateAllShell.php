<?php
/**
 * Installシェル
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
 * @package NetCommons\Install\Console\Command
 */
class UpdateAllShell extends AppShell {

/**
 * Override startup
 *
 * @return void
 */
	public function startup() {
		$this->hr();
		$this->out(__d('plugin_manager', 'Update of all plugins'));
		$this->hr();
	}

/**
 * Override main
 *
 * @return void
 */
	public function main() {
		$this->out(__d('plugin_manager', '[S]tart'));
		$this->out(__d('plugin_manager', '[Q]uit'));

		$choice = strtolower(
			$this->in(__d('net_commons', 'What would you like to do?'), ['S', 'Q'], 'Q')
		);
		switch ($choice) {
			case 's':
				if (! isset($this->PluginUpdateUtil)) {
					$this->PluginUpdateUtil = new PluginUpdateUtil();
				}
				if ($this->PluginUpdateUtil->updateAll()) {
					$this->out('<success>' . __d('plugin_manager', 'Successfully updated of all plugins.') . '</success>');
				} else {
					$this->out('<error>' . __d('plugin_manager', 'Failure updated of all plugins.') . '</error>');
				}
				return $this->_stop();
			case 'q':
				return $this->_stop();
			default:
				$this->out(
					__d('net_commons', 'You have made an invalid selection. ' .
								'Please choose a command to execute by entering %s.', '[S, H, Q]')
				);
		}
		$this->hr();
	}

}
