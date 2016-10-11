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
 * Called before the controller action.
 * You can use this method to configure and customize components
 *  or perform logic that needs to happen before each controller action.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$Plugin = $this->Plugin;
		if (isset($this->params['pass'][0])) {
			$pluginType = $this->params['pass'][0];
		} else {
			$pluginType = $Plugin::PLUGIN_TYPE_FOR_FRAME;
		}
		$this->set('active', $pluginType);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$Plugin = $this->Plugin;

		$plugins = array();
		$pluginsMap = array();

		$typeKey = 'type' . $this->viewVars['active'];
		switch ($this->viewVars['active']) {
			case $Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL:
				$plugins[$typeKey] = $this->Plugin->getPlugins(
					array(Plugin::PLUGIN_TYPE_FOR_SITE_MANAGER, Plugin::PLUGIN_TYPE_FOR_SYSTEM_MANGER)
				);
				$pluginsMap[$typeKey] =
						array_flip(array_keys(Hash::combine($plugins[$typeKey], '{n}.Plugin.key')));
				break;

			case $Plugin::PLUGIN_TYPE_FOR_NOT_YET:
				break;

			case $Plugin::PLUGIN_TYPE_FOR_EXT_COMPOSER:
				$plugins[$typeKey] = $this->Plugin->getExternalPlugins();
				break;

			default:
				$plugins[$typeKey] = $this->Plugin->getPlugins(
					$Plugin::PLUGIN_TYPE_FOR_FRAME
				);
				$pluginsMap[$typeKey] =
					array_flip(array_keys(Hash::combine($plugins[$typeKey], '{n}.Plugin.key')));
		}

		$this->request->data['Plugins'] = Hash::extract($plugins, '{s}.{n}');
		$this->set('plugins', $plugins);
		$this->set('pluginsMap', $pluginsMap);

		$nc3plugin = $this->Plugin->getComposer('netcommons/net-commons');
		$this->set('nc3plugin', $nc3plugin);
	}

/**
 * view method
 *
 * @param int $pluginType Plugin type
 * @param string $pluginKey Plugin key
 * @return void
 */
	public function view($pluginType = null, $pluginKey = null) {
		$Plugin = $this->Plugin;

		if ($pluginType === $Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL) {
			$plugins = $this->Plugin->getPlugins(
				array(Plugin::PLUGIN_TYPE_FOR_SITE_MANAGER, Plugin::PLUGIN_TYPE_FOR_SYSTEM_MANGER),
				$pluginKey
			);
		} elseif ($pluginType === $Plugin::PLUGIN_TYPE_FOR_FRAME) {
			$plugins = $this->Plugin->getPlugins($pluginType, $pluginKey);
		} else {
			$plugins = $this->Plugin->getExternalPlugins($pluginKey);
		}

		if ($plugins) {
			$this->request->data['Plugin'] = $plugins[0]['Plugin'];
			$this->set('plugin', $plugins[0]);
		}

		$nc3plugin = $this->Plugin->getComposer('netcommons/net-commons');
		$this->set('nc3plugin', $nc3plugin);

		$this->set('pluginType', $pluginType);
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		//	if ($this->request->is('post')) {
		//		$this->PluginManager->create();
		//		if ($this->PluginManager->save($this->request->data)) {
		//			$this->Session->setFlash(__('The plugin manager has been saved.'));
		//			return $this->redirect(array('action' => 'index'));
		//		} else {
		//			$this->Session->setFlash(
		//				__('The plugin manager could not be saved. Please, try again.')
		//			);
		//		}
		//	}
		//	$languages = $this->PluginManager->Language->find('list');
		//	$trackableCreators = $this->PluginManager->TrackableCreator->find('list');
		//	$trackableUpdaters = $this->PluginManager->TrackableUpdater->find('list');
		//	$this->set(compact('languages', 'trackableCreators', 'trackableUpdaters'));
	}

/**
 * edit method
 *
 * @param int $pluginType Plugin type
 * @return void
 */
	public function edit($pluginType = null) {
		if (! $this->request->is('put')) {
			$this->throwBadRequest();
			return;
		}

		$plugins = $this->Plugin->getPlugins($pluginType, $this->data['Plugin']['key']);
		if (! $plugins) {
			$this->throwBadRequest();
			return;
		}

		$error = false;

		if (! $this->Plugin->updateComposer($plugins[0]['composer']['name'])) {
			$this->NetCommons->setFlashNotification(
				sprintf(__d('net_commons', 'Failed to proceed the %s.'), 'composer'),
				array('class' => 'danger', 'interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL)
			);
			$error = true;
			return;
		}

		if (! $this->Plugin->runMigration($plugins[0]['Plugin']['key'])) {
			$this->NetCommons->setFlashNotification(
				sprintf(__d('net_commons', 'Failed to proceed the %s.'), 'migration'), array(
				'class' => 'danger',
				'interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL
			));
			$error = true;
			return;
		}

		if (! $this->Plugin->updateBower($plugins[0]['Plugin']['key'])) {
			$this->NetCommons->setFlashNotification(
				sprintf(__d('net_commons', 'Failed to proceed the %s.'), 'bower'),
				array('class' => 'danger', 'interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL)
			);
			$error = true;
			return;
		}

		if (! $error) {
			$this->NetCommons->setFlashNotification(
				__d('net_commons', 'Successfully saved.'), array('class' => 'success')
			);
		}

		$redirectUrl = NetCommonsUrl::actionUrl(array(
			'plugin' => $this->params['plugin'],
			'controller' => $this->params['controller'],
			'action' => 'view',
			$pluginType,
			$this->data['Plugin']['key']
		));
		$this->redirect($redirectUrl);
	}

/**
 * edit method
 *
 * @param int $pluginType Plugin type
 * @throws NotFoundException
 * @return void
 */
	public function order($pluginType = null) {
		if (! $this->request->is('post')) {
			$this->throwBadRequest();
			return;
		}

		$data = $this->data;
		unset($data['save']);
		if (! $this->Plugin->saveWeight($data)) {
			$this->throwBadRequest();
			return;
		}

		$this->NetCommons->setFlashNotification(
			__d('net_commons', 'Successfully saved.'), array('class' => 'success')
		);
		$redirectUrl = NetCommonsUrl::actionUrl(array(
			'plugin' => $this->params['plugin'],
			'controller' => $this->params['controller'],
			'action' => 'index',
			$pluginType,
		));
		$this->redirect($redirectUrl);
	}

/**
 * delete method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function delete($id = null) {
		//	$this->PluginManager->id = $id;
		//	if (!$this->PluginManager->exists()) {
		//		throw new NotFoundException(__('Invalid plugin manager'));
		//	}
		//	$this->request->onlyAllow('post', 'delete');
		//	if ($this->PluginManager->delete()) {
		//		$this->Session->setFlash(__('The plugin manager has been deleted.'));
		//	} else {
		//		$this->Session->setFlash(
		//			__('The plugin manager could not be deleted. Please, try again.')
		//		);
		//	}
		//	return $this->redirect(array('action' => 'index'));
	}
}
