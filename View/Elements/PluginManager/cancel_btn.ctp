<?php
/**
 * PluginManager canel btn template
 *   - $pluginType: plugins.type
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<button type="button" class="btn btn-default btn-workflow" ng-disabled="sending"
		onclick="location.href='<?php echo $this->Html->url('/plugin_manager/plugin_manager/index/' . $pluginType . '/'); ?>'">

	<span class="glyphicon glyphicon-remove"></span>
	<?php echo __d('net_commons', 'Cancel'); ?>
</button>
