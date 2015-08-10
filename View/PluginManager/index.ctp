<?php
/**
 * PluginManager index template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->Html->script(
	array(
		'/plugin_manager/js/plugin_manager.js'
	),
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
);
?>

<?php echo $this->element('PluginManager/title'); ?>

<?php echo $this->element('PluginManager/tabs'); ?>

<div ng-controller="PluginManager"
	 ng-init="initialize(<?php echo h(json_encode(array(
			'plugins' => PluginManagerController::camelizeKeyRecursive($plugins),
			'pluginsMap' => $pluginsMap
		), true)); ?>)">

	<div class="tab-content">
		<?php if ($active === PluginManagerController::TAB_FOR_FRAME) : ?>
			<div class="tab-pane active">
				<?php echo $this->element('PluginManager/plugins', array('pluginType' => Plugin::PLUGIN_TYPE_FOR_FRAME, 'hasFormTag' => true)); ?>
			</div>
		<?php endif; ?>

		<?php if ($active === PluginManagerController::TAB_FOR_NOT_YET) : ?>
			<div class="tab-pane active">
				not_yet_installed
			</div>
		<?php endif; ?>

		<?php if ($active === PluginManagerController::TAB_FOR_CONTROL_PANEL) : ?>
			<div class="tab-pane active">
				<?php echo $this->element('PluginManager/plugins', array('pluginType' => Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL, 'hasFormTag' => true)); ?>
			</div>
		<?php endif; ?>

		<?php if ($active === PluginManagerController::TAB_FOR_EXTERNAL) : ?>
			<div class="tab-pane active">
				<?php echo $this->element('PluginManager/plugins', array('pluginType' => Plugin::PLUGIN_TYPE_FOR_EXTERNAL, 'hasFormTag' => false)); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

