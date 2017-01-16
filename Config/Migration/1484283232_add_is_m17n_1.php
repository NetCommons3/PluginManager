<?php
/**
 * 多言語化対応
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 多言語化対応
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Config\Migration
 */
class AddIsM17n1 extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_is_m17n_1';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		$Plugin = $this->generateModel('Plugin');

		if ($direction === 'up') {
			$count = $Plugin->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'key' => array(
						'access_counters', 'bbses', 'circular_notices', 'iframes', 'menus',
						'rss_readers', 'searches', 'topics'
					),
				)
			));
			if ($count > 0) {
				$update = array(
					'is_m17n' => false
				);
				$conditions = array(
					'key' => array(
						'access_counters', 'bbses', 'circular_notices', 'iframes', 'menus',
						'rss_readers', 'searches', 'topics'
					),
				);
				if (! $Plugin->updateAll($update, $conditions)) {
					return false;
				}
			}

			$update = array(
				'is_m17n' => null
			);
			$conditions = array(
				'type !=' => '1'
			);
			if (! $Plugin->updateAll($update, $conditions)) {
				return false;
			}

			$update = array(
				'is_origin' => true,
				'is_translation' => true,
			);
			$conditions = array(
				'type' => array('1', '2', '3'),
				'language_id' => '2'
			);
			if (! $Plugin->updateAll($update, $conditions)) {
				return false;
			}

			$update = array(
				'is_origin' => false,
				'is_translation' => true,
			);
			$conditions = array(
				'type' => array('1', '2', '3'),
				'language_id' => '1'
			);
			if (! $Plugin->updateAll($update, $conditions)) {
				return false;
			}
		}

		return true;
	}
}
