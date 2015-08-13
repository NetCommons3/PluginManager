<?php
/**
 * PluginManager App Controller
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * PluginManager App Controller
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @package NetCommons\PluginManager\Controller
 */
class PluginManagerAppController extends AppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'PluginManager.Plugin'
	);

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'ControlPanel.ControlPanelLayout',
		'Security',
	);

}
