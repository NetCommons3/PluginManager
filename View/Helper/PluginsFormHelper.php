<?php
/**
 * PluginsFormHelper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');

/**
 * PluginsFormHelper
 *
 * @package NetCommons\PluginManager\View\Helper
 */
class PluginsFormHelper extends AppHelper {

/**
 * 使用ヘルパー
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
	);

/**
 * Before render callback. beforeRender is called before the view file is rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $viewFile The view file that is going to be rendered
 * @return void
 */
	public function beforeRender($viewFile) {
		$this->NetCommonsHtml->css('/plugin_manager/css/style.css');
		parent::beforeRender($viewFile);
	}

/**
 * Roomに対するプラグインチェックボックスリスト
 *
 * @param string $fieldName フィールド名
 * @param array $attributes HTMLの属性オプション
 * @return string HTML
 */
	public function checkboxPluginsRoom($fieldName = 'PluginsRoom.plugin_key', $attributes = array()) {
		$html = '';

		//チェックボックスの設定
		$options = Hash::combine(
			$this->_View->viewVars['pluginsRoom'], '{n}.Plugin.key', '{n}.Plugin.name'
		);

		if (Hash::get($attributes, 'hiddenField', true)) {
			$this->_View->request->data['Plugin']['key'] = array_keys($options);
			foreach (array_keys($this->_View->request->data['Plugin']['key']) as $index) {
				$html .= $this->NetCommonsForm->hidden('Plugin.' . $index . '.key', array(
					'value' => $this->_View->request->data['Plugin']['key'][$index],
				));
			}
		}

		$defaults = Hash::get($attributes, 'default', array());
		$attributes = Hash::remove($attributes, 'default');

		$this->_View->request->data = Hash::insert(
			$this->_View->request->data, $fieldName, $defaults
		);
		$html .= $this->NetCommonsForm->select(
			$fieldName, $options, Hash::merge($attributes, array('multiple' => 'checkbox'))
		);
		return $html;
	}

/**
 * Roomに対するプラグインのセレクトボックス表示
 *
 * @param string $fieldName フィールド名
 * @param array $attributes HTMLの属性オプション
 * @return string HTML
 */
	public function selectPluginsRoom($fieldName, $attributes = array()) {
		$html = '';

		//チェックボックスの設定
		$options = Hash::get(
			$attributes,
			'options',
			Hash::combine($this->_View->viewVars['pluginsRoom'], '{n}.Plugin.key', '{n}.Plugin.name')
		);

		$attributes = Hash::merge(
			array(
				'type' => 'select',
				'options' => $options,
				'label' => __d('plugin_manager', 'Select plugin'),
			),
			$attributes
		);

		$html .= $this->NetCommonsForm->input($fieldName, $attributes);
		return $html;
	}

}
