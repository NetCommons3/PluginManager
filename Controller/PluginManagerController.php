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
App::uses('Plugin', 'PluginManager.Model');
App::uses('NetCommonsComponent', 'NetCommons.Controller/Component');
App::uses('PluginUpdateUtil', 'PluginManager.Utility');

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

		if (isset($this->params['pass'][0])) {
			$pluginType = $this->params['pass'][0];
		} else {
			$pluginType = Plugin::PLUGIN_TYPE_FOR_FRAME;
		}
		$this->set('active', $pluginType);

		if ($this->params['action'] === 'view' && isset($this->params['ext'])) {
			$this->request->params['pass'][] = $this->params['ext'];
			unset($this->params['ext']);
		}
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$plugins = array();
		$pluginsMap = array();

		//versionフィールドがない場合、Migrationを実行する
		if (! $this->Plugin->hasField('version')) {
			$this->Plugin->runMigration('plugin_manager');
			$this->Plugin->runMigration('site_manager');

			return $this->redirect('/plugin_manager/plugin_manager/index/');
		}

		$typeKey = 'type' . $this->viewVars['active'];
		switch ($this->viewVars['active']) {
			case Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL:
				$plugins[$typeKey] = $this->Plugin->getPlugins(
					array(Plugin::PLUGIN_TYPE_FOR_SITE_MANAGER, Plugin::PLUGIN_TYPE_FOR_SYSTEM_MANGER)
				);
				$pluginsMap[$typeKey] = array_flip(
					array_keys(Hash::combine($plugins[$typeKey], '{n}.Plugin.key'))
				);
				break;

			case Plugin::PLUGIN_TYPE_FOR_NOT_YET:
				$plugins[$typeKey] = $this->Plugin->getNewPlugins($this->viewVars['active']);
				$pluginsMap[$typeKey] = array_flip(
					array_keys(Hash::combine($plugins[$typeKey], '{n}.Plugin.key'))
				);
				break;

			case Plugin::PLUGIN_TYPE_FOR_THEME:
			case Plugin::PLUGIN_TYPE_FOR_EXT_COMPOSER:
			case Plugin::PLUGIN_TYPE_FOR_EXT_BOWER:
				$plugins[$typeKey] = array_merge(
					$this->Plugin->getPlugins($this->viewVars['active']),
					$this->Plugin->getNewPlugins($this->viewVars['active'])
				);

				$pluginsMap[$typeKey] = array_flip(
					array_keys(Hash::combine($plugins[$typeKey], '{n}.Plugin.key'))
				);
				break;

			default:
				$plugins[$typeKey] = $this->Plugin->getPlugins(
					$this->viewVars['active']
				);
				$pluginsMap[$typeKey] = array_flip(
					array_keys(Hash::combine($plugins[$typeKey], '{n}.Plugin.key'))
				);
		}

		$this->request->data['Plugins'] = Hash::extract($plugins, '{s}.{n}');

		$this->set('plugins', $plugins);
		$this->set('pluginsMap', $pluginsMap);

		$nc3plugin = $this->Plugin->getPlugins(
			Plugin::PLUGIN_TYPE_CORE, 'net_commons'
		);
		$this->set('nc3plugin', $nc3plugin);

		$this->set('hasNewPlugin', (bool)$this->Plugin->getNewPlugins(Plugin::PLUGIN_TYPE_FOR_NOT_YET));
		$this->set('hasUpdate', $this->Plugin->hasUpdate());
	}

/**
 * view method
 *
 * @return void
 */
	public function view() {
		$pluginType = array_shift($this->request->params['pass']);
		$pluginKey = implode('/', $this->params['pass']);
		if (isset($this->params['ext'])) {
			$pluginKey .= '.' . $this->params['ext'];
		}
		if ($pluginType === Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL) {
			$plugins = $this->Plugin->getPlugins(
				array(Plugin::PLUGIN_TYPE_FOR_SITE_MANAGER, Plugin::PLUGIN_TYPE_FOR_SYSTEM_MANGER),
				$pluginKey
			);
		} elseif ($pluginType === Plugin::PLUGIN_TYPE_FOR_NOT_YET) {
			$plugins = $this->Plugin->getNewPlugins($pluginType, $pluginKey);
		} else {
			$plugins = $this->Plugin->getPlugins($pluginType, $pluginKey);
		}

		if ($plugins) {
			$this->request->data['Plugin'] = $plugins['Plugin'];
			$this->set('plugin', $plugins);
		}

		$nc3plugin = $this->Plugin->getPlugins(
			Plugin::PLUGIN_TYPE_CORE, 'net_commons'
		);
		$this->set('nc3plugin', $nc3plugin);

		$this->set('pluginType', $pluginType);

		//レイアウトの設定
		$this->viewClass = 'View';
		$this->layout = 'NetCommons.modal';
	}

/**
 * edit method
 *
 * @param int $pluginType Plugin type
 * @return void
 */
	public function edit($pluginType = null) {
		if (! $this->request->is(array('post', 'put'))) {
			return $this->throwBadRequest();
		}

		$plugin = $this->Plugin->getPlugins($pluginType, $this->data['Plugin']['key']);
		if (! $plugin) {
			$plugin = $this->Plugin->getNewPlugins($pluginType, $this->data['Plugin']['key']);
		}
		if (! $plugin) {
			return $this->throwBadRequest();
		}

		if ($this->Plugin->runVersionUp($plugin)) {
			$this->NetCommons->setFlashNotification(
				__d('net_commons', 'Successfully saved.'), array('class' => 'success')
			);
		} else {
			$this->NetCommons->setFlashNotification(
				sprintf(__d('net_commons', 'Failed to proceed the %s.'), 'migration'), array(
				'class' => 'danger',
				'interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL
			));
		}

		if ($pluginType === Plugin::PLUGIN_TYPE_FOR_NOT_YET &&
				!(bool)$this->Plugin->getNewPlugins(Plugin::PLUGIN_TYPE_FOR_NOT_YET)) {
			$redirectUrl = NetCommonsUrl::actionUrl(array(
				'plugin' => $this->params['plugin'],
				'controller' => $this->params['controller'],
				'action' => 'index'
			));
		} else {
			$redirectUrl = NetCommonsUrl::actionUrl(array(
				'plugin' => $this->params['plugin'],
				'controller' => $this->params['controller'],
				'action' => 'index',
				$pluginType,
			));
		}
		return $this->redirect($redirectUrl);
	}

/**
 * 一括アップデート
 *
 * @param int $pluginType Plugin type
 * @return void
 */
	public function update_all($pluginType = null) {
		if (! $this->request->is('post')) {
			return $this->throwBadRequest();
		}

		if (! isset($this->PluginUpdateUtil)) {
			$this->PluginUpdateUtil = new PluginUpdateUtil();
		}

		if ($this->PluginUpdateUtil->updateAll()) {
			$this->NetCommons->setFlashNotification(
				__d('net_commons', 'Successfully saved.'), array('class' => 'success')
			);
		} else {
			$this->NetCommons->setFlashNotification(
				__d('plugin_manager', 'Failure updated of all plugins.'),
				array(
					'class' => 'danger',
					'interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL
				)
			);
		}

		$redirectUrl = NetCommonsUrl::actionUrl(array(
			'plugin' => $this->params['plugin'],
			'controller' => $this->params['controller'],
			'action' => 'index'
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
}
