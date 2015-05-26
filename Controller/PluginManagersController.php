<?php
App::uses('AppController', 'Controller');
/**
 * PluginManagers Controller
 *
 * @property PluginManager $PluginManager
 * @property PaginatorComponent $Paginator
 *
* @author Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link http://www.netcommons.org NetCommons Project
* @license http://www.netcommons.org/license.txt NetCommons License
 */
class PluginManagersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->PluginManager->recursive = 0;
		$this->set('pluginManagers', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function view($id = null) {
		if (!$this->PluginManager->exists($id)) {
			throw new NotFoundException(__('Invalid plugin manager'));
		}
		$options = array('conditions' => array('PluginManager.' . $this->PluginManager->primaryKey => $id));
		$this->set('pluginManager', $this->PluginManager->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->PluginManager->create();
			if ($this->PluginManager->save($this->request->data)) {
				$this->Session->setFlash(__('The plugin manager has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The plugin manager could not be saved. Please, try again.'));
			}
		}
		$languages = $this->PluginManager->Language->find('list');
		$trackableCreators = $this->PluginManager->TrackableCreator->find('list');
		$trackableUpdaters = $this->PluginManager->TrackableUpdater->find('list');
		$this->set(compact('languages', 'trackableCreators', 'trackableUpdaters'));
	}

/**
 * edit method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function edit($id = null) {
		if (!$this->PluginManager->exists($id)) {
			throw new NotFoundException(__('Invalid plugin manager'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->PluginManager->save($this->request->data)) {
				$this->Session->setFlash(__('The plugin manager has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The plugin manager could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('PluginManager.' . $this->PluginManager->primaryKey => $id));
			$this->request->data = $this->PluginManager->find('first', $options);
		}
		$languages = $this->PluginManager->Language->find('list');
		$trackableCreators = $this->PluginManager->TrackableCreator->find('list');
		$trackableUpdaters = $this->PluginManager->TrackableUpdater->find('list');
		$this->set(compact('languages', 'trackableCreators', 'trackableUpdaters'));
	}

/**
 * delete method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function delete($id = null) {
		$this->PluginManager->id = $id;
		if (!$this->PluginManager->exists()) {
			throw new NotFoundException(__('Invalid plugin manager'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->PluginManager->delete()) {
			$this->Session->setFlash(__('The plugin manager has been deleted.'));
		} else {
			$this->Session->setFlash(__('The plugin manager could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
