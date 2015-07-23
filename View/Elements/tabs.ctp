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
	<li class="<?php echo ($active === PluginManagerController::TAB_FOR_FRAME ? 'active' : $disabled); ?>">
		<a href="#<?php echo PluginManagerController::TAB_FOR_FRAME; ?>" role="tab" data-toggle="tab">
			<?php echo __d('plugin_manager', 'Installed plugins'); ?>
		</a>
	</li>

	<li class="<?php echo ($active === PluginManagerController::TAB_FOR_NOT_YET ? 'active' : $disabled); ?>">
		<a href="#<?php echo PluginManagerController::TAB_FOR_NOT_YET; ?>" role="tab" data-toggle="tab">
			<?php echo __d('plugin_manager', 'Plugins not yet installed'); ?>
		</a>
	</li>

	<li class="<?php echo ($active === PluginManagerController::TAB_FOR_CONTROL_PANEL ? 'active' : $disabled); ?>">
		<a href="#<?php echo PluginManagerController::TAB_FOR_CONTROL_PANEL; ?>" role="tab" data-toggle="tab">
			<?php echo __d('plugin_manager', 'System plugins'); ?>
		</a>
	</li>
</ul>

<br />
