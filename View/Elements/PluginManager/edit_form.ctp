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

		<?php echo $this->NetCommonsForm->create('Plugin', array(
				'class' => 'form-inline',
				'url' => $this->NetCommonsHtml->url(array('action' => 'edit', $pluginType))
			)); ?>

			<?php echo $this->NetCommonsForm->hidden('Plugin.key'); ?>

			<?php echo $this->Button->cancelAndSave(
					__d('net_commons', 'Cancel'),
					__d('plugin_manager', 'Update'),
					$this->NetCommonsHtml->url(array('action' => 'index', $pluginType)),
					array(),
					array(
						'onclick' => 'return confirm(\'' . __d('plugin_manager', 'Do you want to update the plugin?') . '\')'
					)
				); ?>

		<?php echo $this->NetCommonsForm->end(); ?>

	<?php else : ?>

		<?php echo $this->Button->cancel(
				__d('net_commons', 'Cancel'),
				$this->NetCommonsHtml->url(array('action' => 'index', $pluginType))
			); ?>

	<?php endif; ?>
</div>
