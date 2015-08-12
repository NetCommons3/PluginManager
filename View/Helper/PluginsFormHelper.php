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

App::uses('FormHelper', 'View/Helper');

/**
 * PluginsFormHelper
 *
 * @package NetCommons\PluginManager\View\Helper
 */
class PluginsFormHelper extends FormHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array('Form');

/**
 * Outputs room plugins
 *
 * @param string $roomId rooms.id
 * @param array $attributes The HTML attributes of the select element.
 * @return string Formatted CHECKBOX element
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
 */
	public function checkboxPluginsRoom($roomId, $attributes = array()) {
		$html = '';

		//Modelの呼び出し
		$this->Plugin = ClassRegistry::init('PluginManager.Plugin');
		$this->PluginsRoom = ClassRegistry::init('PluginManager.PluginsRoom');

		//findのoptionsセット
		$findOptions = array(
			'fields' => array(
				$this->Plugin->alias . '.key',
				$this->Plugin->alias . '.name',
				$this->PluginsRoom->alias . '.room_id',
				$this->PluginsRoom->alias . '.plugin_key'
			),
			'conditions' => array(
				$this->Plugin->alias . '.type' => Plugin::PLUGIN_TYPE_FOR_FRAME,
				$this->Plugin->alias . '.language_id' => Configure::read('Config.languageId'),
			),
			'order' => array($this->Plugin->alias . '.weight' => 'asc')
		);

		//データ取得
		if (isset($attributes['all']) && $attributes['all']) {
			$plugins = $this->Plugin->find('all', Hash::merge($findOptions, array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => $this->PluginsRoom->table,
						'alias' => $this->PluginsRoom->alias,
						'type' => 'LEFT',
						'conditions' => array(
							$this->Plugin->alias . '.key' . ' = ' . $this->PluginsRoom->alias . ' .plugin_key',
							$this->PluginsRoom->alias . '.room_id' => $roomId,
						),
					)
				),
			)));
			unset($attributes['all']);
		} else {
			$plugins = $this->PluginsRoom->find('all', Hash::merge($findOptions, array(
				'recursive' => 0,
				'conditions' => array(
					$this->PluginsRoom->alias . '.room_id' => $roomId,
				),
			)));
		}

		//チェックボックスの設定
		$options = Hash::combine($plugins, '{n}.Plugin.key', '{n}.Plugin.name');
		$this->_View->request->data['Plugin']['key'] = array_keys($options);
		foreach (array_keys($this->_View->request->data['Plugin']['key']) as $index) {
			$html .= $this->Form->hidden('Plugin.' . $index . '.key');
		}

		$defaults = Hash::extract($plugins, '{n}.PluginsRoom[room_id=' . $roomId . ']');
		$defaults = array_values(Hash::combine($defaults, '{n}.plugin_key', '{n}.plugin_key'));

		$this->_View->request->data['PluginsRoom']['plugin_key'] = $defaults;
		$html .= $this->Form->select('PluginsRoom.plugin_key', $options, Hash::merge($attributes, array(
			'multiple' => 'checkbox',
		)));
		return $html;
	}

}