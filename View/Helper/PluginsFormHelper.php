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
 * @param string $fieldName Name attribute of the CHECKBOX
 * @param array $attributes The HTML attributes of the select element.
 * @return string Formatted CHECKBOX element
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
 */
	public function checkboxPluginsRoom($fieldName, $roomId, $attributes = array()) {
		list($model, $key) = explode('.', $fieldName);

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
		$defaults = Hash::extract($plugins, '{n}.PluginsRoom[room_id=' . $roomId . ']');
		$defaults = array_values(Hash::combine($defaults, '{n}.plugin_key', '{n}.plugin_key'));

		$this->_View->request->data[$model][$key] = $defaults;

		$html = '';
		$html .= $this->Form->select($fieldName, $options, Hash::merge($attributes, array(
			'multiple' => 'checkbox',
		)));
		return $html;
	}

/**
 * Outputs space plugins
 *
 * @param string $fieldName Name attribute of the CHECKBOX
 * @param array $attributes The HTML attributes of the select element.
 * @return string Formatted CHECKBOX element
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
 */
	public function checkboxPluginsSpace($fieldName, $spaceId, $attributes = array()) {
		list($model, $key) = explode('.', $fieldName);

		//Modelの呼び出し
		$this->Plugin = ClassRegistry::init('PluginManager.Plugin');
		$this->PluginsSpace = ClassRegistry::init('PluginManager.PluginsSpace');

		//findのoptionsセット
		$findOptions = array(
			'fields' => array(
				$this->Plugin->alias . '.key',
				$this->Plugin->alias . '.name',
				$this->PluginsSpace->alias . '.space_id',
				$this->PluginsSpace->alias . '.plugin_key'
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
						'table' => $this->PluginsSpace->table,
						'alias' => $this->PluginsSpace->alias,
						'type' => 'LEFT',
						'conditions' => array(
							$this->Plugin->alias . '.key' . ' = ' . $this->PluginsSpace->alias . ' .plugin_key',
							$this->PluginsSpace->alias . '.space_id' => $spaceId,
						),
					)
				),
			)));
			unset($attributes['all']);

		} else {
			$plugins = $this->PluginsSpace->find('all', Hash::merge($findOptions, array(
				'recursive' => 0,
				'conditions' => array(
					$this->PluginsSpace->alias . '.space_id' => $spaceId,
				),
			)));
		}

		//チェックボックスの設定
		$options = Hash::combine($plugins, '{n}.Plugin.key', '{n}.Plugin.name');
		$defaults = Hash::extract($plugins, '{n}.PluginsSpace[space_id=' . $spaceId . ']');
		$defaults = array_values(Hash::combine($defaults, '{n}.plugin_key', '{n}.plugin_key'));

		$this->_View->request->data[$model][$key] = $defaults;

		$html = '';
		$html .= $this->Form->select($fieldName, $options, Hash::merge($attributes, array(
			'multiple' => 'checkbox',
		)));
		return $html;
	}

}
