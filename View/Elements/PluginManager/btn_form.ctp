<?php
/**
 * PluginManager btn form template
 *   - $pluginType: plugins.type
 *   - $plugin: Plugin data
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="panel-footer text-center">
	<?php if ($pluginType === Plugin::PLUGIN_TYPE_FOR_FRAME || $pluginType === Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL) : ?>
		<?php echo $this->Form->create(null, array('novalidate' => true, 'class' => 'form-inline', 'url' => '/plugin_manager/plugin_manager/update/' . $pluginType)); ?>
			<?php echo $this->Form->hidden('Plugin.key', array(
					'value' => $plugin['Plugin']['key'],
				)); ?>

			<?php echo $this->element('PluginManager/cancel_btn'); ?>

			<?php echo $this->Form->button(__d('plugin_manager', 'Update'), array(
					'class' => 'btn btn-primary btn-workflow',
					'name' => 'save',
					'ng-click' => 'sending=true; save()',
					'ng-disabled' => 'sending',
					'onclick' => 'return confirm(\'' . __d('plugin_manager', 'Do you want to update the plugin?') . '\')'
				)); ?>

		<?php echo $this->Form->end(); ?>
	<?php else : ?>
		<?php echo $this->element('PluginManager/cancel_btn'); ?>
	<?php endif; ?>
</div>
