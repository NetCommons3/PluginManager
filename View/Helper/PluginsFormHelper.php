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
	);

/**
 * Roomに対するプラグインチェックボックスリスト
 *
 * @param array $attributes The HTML attributes of the select element.
 * @return string Formatted CHECKBOX element
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
 */
	public function checkboxPluginsRoom($attributes = array()) {
		$html = '';

		//チェックボックスの設定
		$options = Hash::combine($this->_View->viewVars['pluginsRoom'], '{n}.Plugin.key', '{n}.Plugin.name');
		$this->_View->request->data['Plugin']['key'] = array_keys($options);
		foreach (array_keys($this->_View->request->data['Plugin']['key']) as $index) {
			$html .= $this->NetCommonsForm->hidden('Plugin.' . $index . '.key', array(
				'value' => $this->_View->request->data['Plugin']['key'][$index],
			));
		}

		$defaults = Hash::extract($this->_View->viewVars['pluginsRoom'], '{n}.PluginsRoom[room_id=' . Current::read('Room.id') . ']');
		$defaults = array_values(Hash::combine($defaults, '{n}.plugin_key', '{n}.plugin_key'));

		$this->_View->request->data['PluginsRoom']['plugin_key'] = $defaults;
		$html .= $this->NetCommonsForm->select('PluginsRoom.plugin_key', $options, Hash::merge($attributes, array(
			'multiple' => 'checkbox',
		)));
		return $html;
	}

}
