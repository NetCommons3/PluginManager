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

/**
 * 一括アップデートシェル
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Console\Command
 */
class UpdateAllShell extends AppShell {

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 */
	public $tasks = array(
		'PluginManager.UpdateAll',
	);

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
		$this->out(__d('net_commons', '[H]elp'));

		$choice = strtolower(
			$this->in(__d('net_commons', 'What would you like to do?'), ['S', 'Q', 'H'], 'Q')
		);
		switch ($choice) {
			case 's':
				$this->UpdateAll->execute();
				return $this->_stop();
			case 'q':
				return $this->_stop();
			case 'h':
				$this->out($this->getOptionParser()->help());
				break;
			default:
				$this->out(
					__d('net_commons', 'You have made an invalid selection. Please choose a command to execute by entering %s.', '[S, H, Q]')
				);
		}
		$this->hr();
	}

/**
 * Get the option parser.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('install', 'NetCommons Install'))
			->addSubcommand('update_all', array(
				'help' => __d('plugin_manager', 'Update of all plugins'),
				'parser' => $this->UpdateAll->getOptionParser(),
			));
	}

}
