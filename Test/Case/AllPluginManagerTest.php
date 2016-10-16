<?php
/**
 * PluginManager All Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsTestSuite', 'NetCommons.TestSuite');

/**
 * PluginManager All Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Test\Case
 * @codeCoverageIgnore
 */
class AllPluginManagerTest extends NetCommonsTestSuite {

/**
 * All test suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$plugin = preg_replace('/^All([\w]+)Test$/', '$1', __CLASS__);
		$suite = new NetCommonsTestSuite(sprintf('All %s Plugin tests', $plugin));
		$suite->addTestDirectoryRecursive(CakePlugin::path($plugin) . 'Test' . DS . 'Case');

		$Folder = new Folder(CakePlugin::path($plugin) . 'Test' . DS . 'Case');
		$files = $Folder->tree(null, true, 'files');
		foreach ($files as $file) {
			if (preg_match('/\/All([\w]+)Test\.php$/', $file)) {
				continue;
			}
			if (substr($file, -8) === 'Test.php') {
				var_dump($file);
			}
		}
		return $suite;
	}
}
