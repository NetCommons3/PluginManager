<?php
/**
 * PluginManager Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginManagerAppController', 'PluginManager.Controller');

/**
 * PluginManager Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Controller
 */
class PluginManagerController extends PluginManagerAppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$Plugin = $this->Plugin;

		$plugins['type' . $Plugin::PLUGIN_TYPE_FOR_FRAME] = $this->Plugin->getPlugins(
			$Plugin::PLUGIN_TYPE_FOR_FRAME,
			Configure::read('Config.languageId')
		);

		$plugins['type' . $Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL] = $this->Plugin->getPlugins(
			$Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL,
			Configure::read('Config.languageId')
		);

		$pluginsMap['type' . $Plugin::PLUGIN_TYPE_FOR_FRAME] =
				array_flip(array_keys(Hash::combine($plugins['type' . $Plugin::PLUGIN_TYPE_FOR_FRAME], '{n}.Plugin.key')));

		$pluginsMap['type' . $Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL] =
				array_flip(array_keys(Hash::combine($plugins['type' . $Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL], '{n}.Plugin.key')));

		$this->ControlPanelLayout->plugins = $plugins['type' . $Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL];

		$this->set('plugins', $plugins);
		$this->set('pluginsMap', $pluginsMap);

		$this->set('active', 'installed');
	}

/**
 * view method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function view($id = null) {
		//if (!$this->PluginManager->exists($id)) {
		//	throw new NotFoundException(__('Invalid plugin manager'));
		//}
		//$options = array('conditions' => array('PluginManager.' . $this->PluginManager->primaryKey => $id));
		//$this->set('pluginManager', $this->PluginManager->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	//public function add() {
	//	if ($this->request->is('post')) {
	//		$this->PluginManager->create();
	//		if ($this->PluginManager->save($this->request->data)) {
	//			$this->Session->setFlash(__('The plugin manager has been saved.'));
	//			return $this->redirect(array('action' => 'index'));
	//		} else {
	//			$this->Session->setFlash(__('The plugin manager could not be saved. Please, try again.'));
	//		}
	//	}
	//	$languages = $this->PluginManager->Language->find('list');
	//	$trackableCreators = $this->PluginManager->TrackableCreator->find('list');
	//	$trackableUpdaters = $this->PluginManager->TrackableUpdater->find('list');
	//	$this->set(compact('languages', 'trackableCreators', 'trackableUpdaters'));
	//}

/**
 * edit method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function order() {
		if (! $this->request->isPost()) {
			$this->throwBadRequest();
			return;
		}

		if (! $this->PluginManager->saveWeight($this->data)) {
			$this->throwBadRequest();
			return;
		}

		$this->setFlashNotification(__d('net_commons', 'Successfully saved.'), array('class' => 'success'));
		if (! $this->request->is('ajax')) {
			$this->redirect($this->request->referer());
		}


		//if (!$this->PluginManager->exists($id)) {
		//	throw new NotFoundException(__('Invalid plugin manager'));
		//}
		//if ($this->request->is(array('post', 'put'))) {
		//	if ($this->PluginManager->save($this->request->data)) {
		//		$this->Session->setFlash(__('The plugin manager has been saved.'));
		//		return $this->redirect(array('action' => 'index'));
		//	} else {
		//		$this->Session->setFlash(__('The plugin manager could not be saved. Please, try again.'));
		//	}
		//} else {
		//	$options = array('conditions' => array('PluginManager.' . $this->PluginManager->primaryKey => $id));
		//	$this->request->data = $this->PluginManager->find('first', $options);
		//}
		//$languages = $this->PluginManager->Language->find('list');
		//$trackableCreators = $this->PluginManager->TrackableCreator->find('list');
		//$trackableUpdaters = $this->PluginManager->TrackableUpdater->find('list');
		//$this->set(compact('languages', 'trackableCreators', 'trackableUpdaters'));
	}

/**
 * delete method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	//public function delete($id = null) {
	//	$this->PluginManager->id = $id;
	//	if (!$this->PluginManager->exists()) {
	//		throw new NotFoundException(__('Invalid plugin manager'));
	//	}
	//	$this->request->onlyAllow('post', 'delete');
	//	if ($this->PluginManager->delete()) {
	//		$this->Session->setFlash(__('The plugin manager has been deleted.'));
	//	} else {
	//		$this->Session->setFlash(__('The plugin manager could not be deleted. Please, try again.'));
	//	}
	//	return $this->redirect(array('action' => 'index'));
	//}
}
