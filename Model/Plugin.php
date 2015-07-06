<?php
/**
 * Plugin Model
 *
 * @property Language $Language
 * @property File $File
 * @property Role $Role
 * @property Room $Room
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('AppModel', 'Model');

/**
 * Summary for Plugin Model
 */
class Plugin extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'language_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'key' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'namespace' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'default_action' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Language' => array(
			'className' => 'M17n.Language',
			'foreignKey' => 'language_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		/* 'File' => array( */
		/* 	'className' => 'File', */
		/* 	'joinTable' => 'files_plugins', */
		/* 	'foreignKey' => 'plugin_id', */
		/* 	'associationForeignKey' => 'file_id', */
		/* 	'unique' => 'keepExisting', */
		/* 	'conditions' => '', */
		/* 	'fields' => '', */
		/* 	'order' => '', */
		/* 	'limit' => '', */
		/* 	'offset' => '', */
		/* 	'finderQuery' => '', */
		/* ), */
		/* 'Role' => array( */
		/* 	'className' => 'Role', */
		/* 	'joinTable' => 'plugins_roles', */
		/* 	'foreignKey' => 'plugin_id', */
		/* 	'associationForeignKey' => 'role_id', */
		/* 	'unique' => 'keepExisting', */
		/* 	'conditions' => '', */
		/* 	'fields' => '', */
		/* 	'order' => '', */
		/* 	'limit' => '', */
		/* 	'offset' => '', */
		/* 	'finderQuery' => '', */
		/* ), */
		/* 'Room' => array( */
		/* 	'className' => 'Room', */
		/* 	'joinTable' => 'plugins_rooms', */
		/* 	'foreignKey' => 'plugin_id', */
		/* 	'associationForeignKey' => 'room_id', */
		/* 	'unique' => 'keepExisting', */
		/* 	'conditions' => '', */
		/* 	'fields' => '', */
		/* 	'order' => '', */
		/* 	'limit' => '', */
		/* 	'offset' => '', */
		/* 	'finderQuery' => '', */
		/* ) */
	);

/**
 * get plugins for select box options
 *
 * @param array $options find options
 * @return array select box options
 */
	public function getForOptions($options) {
		$options = Hash::merge(['recursive' => -1], $options);

		$plugins = $this->find('all', $options);
		$map = [];
		foreach ($plugins as $plugin) {
			$map[$plugin[$this->alias]['key']] = $plugin[$this->alias]['name'];
		}

		return $map;
	}

/**
 * get plugins for select box options
 *
 * @param array $options find options
 * @return array select box options
 */
	public function getKeyIndexedHash($options) {
		$options = Hash::merge(['recursive' => -1], $options);

		$plugins = $this->find('all', $options);
		$map = [];
		foreach ($plugins as $plugin) {
			$map[$plugin[$this->alias]['key']] = $plugin;
		}

		return $map;
	}
}
