<?php
/**
 * PluginsFormComponent
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * PluginsFormComponent
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Controller\Component
 */
class PluginsFormComponent extends Component {

/**
 * ルームID
 *
 * $roomIdがnullの場合、Current::read('Room.id')を使用する
 *
 * @var string
 */
	public $roomId = null;

/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * @param Controller $controller Controller with components to startup
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::startup
 */
	public function startup(Controller $controller) {
		if (! $this->roomId) {
			$this->roomId = Current::read('Room.id');
		}
	}

/**
 * beforeRender
 *
 * @param Controller $controller Controller
 * @return void
 * @throws NotFoundException
 */
	public function beforeRender(Controller $controller) {
		//RequestActionの場合、スキップする
		if (! empty($controller->request->params['requested'])) {
			return;
		}

		$this->setPluginsRoomForCheckbox($controller);
	}

/**
 * PluginsFormHelper::checkboxPluginsRoom()のためデータをセット
 *
 * @param Controller $controller Controller with components to startup
 * @return void
 */
	public function setPluginsRoomForCheckbox(Controller $controller) {
		//Modelの呼び出し
		$Plugin = ClassRegistry::init('PluginManager.Plugin');
		$PluginsRoom = ClassRegistry::init('PluginManager.PluginsRoom');

		//findのoptionsセット
		$findOptions = array(
			'fields' => array(
				$Plugin->alias . '.key',
				$Plugin->alias . '.name',
				$PluginsRoom->alias . '.room_id',
				$PluginsRoom->alias . '.plugin_key'
			),
			'conditions' => array(
				$Plugin->alias . '.type' => Plugin::PLUGIN_TYPE_FOR_FRAME,
				$Plugin->alias . '.language_id' => Current::read('Language.id'),
			),
			'order' => array(
				$Plugin->alias . '.weight' => 'asc',
				$Plugin->alias . '.id' => 'asc',
			)
		);

		//データ取得
		$pluginsRoom = $Plugin->find('all', Hash::merge($findOptions, array(
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => $PluginsRoom->table,
					'alias' => $PluginsRoom->alias,
					'type' => 'LEFT',
					'conditions' => array(
						$Plugin->alias . '.key' . ' = ' . $PluginsRoom->alias . ' .plugin_key',
						$PluginsRoom->alias . '.room_id' => $this->roomId,
					),
				)
			),
		)));

		$controller->set('pluginsRoom', $pluginsRoom);
		$controller->helpers[] = 'PluginManager.PluginsForm';
	}

}
