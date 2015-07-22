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
?>

<?php echo $this->Html->script('/plugin_manager/js/plugin_manager.js'); ?>

<div ng-controller="PluginManager" class="nc-content-list"
	 ng-init="initialize(<?php echo h(json_encode(array(
			'plugins' => PluginManagerController::camelizeKeyRecursive($plugins),
			'pluginsMap' => PluginManagerController::camelizeKeyRecursive($pluginsMap)
		), true)); ?>)">

	<article>
		<?php echo $this->element('PluginManager.title'); ?>

		<?php echo $this->element('PluginManager.tabs'); ?>

		<div class="tab-content">
			<div class="tab-pane<?php echo ($active === 'installed' ? ' active' : ''); ?>" id="installed">
				<?php echo $this->element('PluginManager/plugins', array('pluginType' => Plugin::PLUGIN_TYPE_FOR_FRAME)); ?>
			</div>
			<div class="tab-pane<?php echo ($active === 'not_yet_installed' ? ' active' : ''); ?>" id="not_yet_installed">
				not_yet_installed
			</div>
			<div class="tab-pane<?php echo ($active === 'system_plugins' ? ' active' : ''); ?>" id="system_plugins">
				<?php echo $this->element('PluginManager/plugins', array('pluginType' => Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL)); ?>
			</div>
		</div>
	</article>
</div>

