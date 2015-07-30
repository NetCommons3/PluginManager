<?php
/**
 * Setting tabs template
 *   - $active: Active tab key. Value is 'block_index or 'frame_settings' or 'role_permissions'.
 *   - $disabled: Disabled tab
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<ul class="nav nav-tabs" role="tablist">
	<li class="<?php echo ($active === PluginManagerController::TAB_FOR_FRAME ? 'active' : ''); ?>">
		<a href="<?php echo $this->Html->url('/plugin_manager/plugin_manager/index/' . Plugin::PLUGIN_TYPE_FOR_FRAME . '/'); ?>">
			<?php echo __d('plugin_manager', 'Installed plugins'); ?>
		</a>
	</li>

	<li class="<?php echo ($active === PluginManagerController::TAB_FOR_NOT_YET ? 'active' : ''); ?>">
		<a href="<?php echo $this->Html->url('/plugin_manager/plugin_manager/index/' . Plugin::PLUGIN_TYPE_FOR_NOT_YET . '/'); ?>">
			<?php echo __d('plugin_manager', 'Plugins not yet installed'); ?>
		</a>
	</li>

	<li class="<?php echo ($active === PluginManagerController::TAB_FOR_CONTROL_PANEL ? 'active' : ''); ?>">
		<a href="<?php echo $this->Html->url('/plugin_manager/plugin_manager/index/' . Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL . '/'); ?>">
			<?php echo __d('plugin_manager', 'System plugins'); ?>
		</a>
	</li>

	<li class="<?php echo ($active === PluginManagerController::TAB_FOR_EXTERNAL ? 'active' : ''); ?>">
		<a href="<?php echo $this->Html->url('/plugin_manager/plugin_manager/index/' . Plugin::PLUGIN_TYPE_FOR_EXTERNAL . '/'); ?>">
			<?php echo __d('plugin_manager', 'External libraries'); ?>
		</a>
	</li>
</ul>

<br>
