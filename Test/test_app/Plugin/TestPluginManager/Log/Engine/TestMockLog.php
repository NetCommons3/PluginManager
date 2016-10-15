<?php
/**
 * PluginManagerテスト用Log出力
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BaseLog', 'Log/Engine');

/**
 * PluginManagerテスト用Log出力
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\test_app\Plugin\TestInstall\Log\Engine
 */
class TestMockLog extends BaseLog {

/**
 * Output stream
 *
 * var array
 */
	public $output = array();

/**
 * Constructs a new Console Logger.
 *
 * Config
 *
 * - `types` string or array, levels the engine is interested in
 * - `scopes` string or array, scopes the engine is interested in
 * - `stream` the path to save logs on.
 * - `outputAs` integer or ConsoleOutput::[RAW|PLAIN|COLOR]
 *
 * @param array $config Options for the FileLog, see above.
 * @throws CakeLogException
 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}

/**
 * ログ出力
 *
 * @param string $type The type of log you are making.
 * @param string $message The message you want to log.
 * @return bool success of write.
 */
	public function write($type, $message) {
		$output = ucfirst($type) . ': ' . $message;
		$this->output[] = $output;
		return true;
	}

}
