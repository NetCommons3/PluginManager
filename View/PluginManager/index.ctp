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

echo $this->NetCommonsHtml->script('/plugin_manager/js/plugin_manager.js');
echo $this->NetCommonsHtml->css('/plugin_manager/css/style.css');
?>

<?php echo $this->element('PluginManager/title'); ?>
<?php echo $this->element('PluginManager/tabs'); ?>

<div ng-controller="PluginManager"
	 ng-init="initialize(<?php echo h(json_encode(array(
			'plugins' => NetCommonsAppController::camelizeKeyRecursive($plugins),
			'pluginsMap' => $pluginsMap
		), true)); ?>)">

	<div class="tab-content">
		<?php if ($active === Plugin::PLUGIN_TYPE_FOR_FRAME) : ?>
			<div class="tab-pane active">
				<?php echo $this->element('PluginManager/plugins',
						array('pluginType' => Plugin::PLUGIN_TYPE_FOR_FRAME, 'hasFormTag' => true)); ?>
			</div>
		<?php endif; ?>

		<?php if ($active === Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL) : ?>
			<div class="tab-pane active">
				<?php echo $this->element('PluginManager/plugins',
						array('pluginType' => Plugin::PLUGIN_TYPE_FOR_CONTROL_PANEL, 'hasFormTag' => true)); ?>
			</div>
		<?php endif; ?>

		<?php if ($active === Plugin::PLUGIN_TYPE_CORE) : ?>
			<div class="tab-pane active">
				<?php echo $this->element('PluginManager/plugins',
						array('pluginType' => Plugin::PLUGIN_TYPE_CORE, 'hasFormTag' => false)); ?>
			</div>
		<?php endif; ?>

		<?php if ($active === Plugin::PLUGIN_TYPE_FOR_NOT_YET) : ?>
			<div class="tab-pane active">

			</div>
		<?php endif; ?>

		<?php if ($active === Plugin::PLUGIN_TYPE_FOR_EXT_COMPOSER) : ?>
			<div class="tab-pane active">
				<?php echo $this->element('PluginManager/plugins',
						array('pluginType' => Plugin::PLUGIN_TYPE_FOR_EXT_COMPOSER, 'hasFormTag' => false)); ?>
			</div>
		<?php endif; ?>

		<?php if ($active === Plugin::PLUGIN_TYPE_FOR_EXT_BOWER) : ?>
			<div class="tab-pane active">
				<?php echo $this->element('PluginManager/plugins',
						array('pluginType' => Plugin::PLUGIN_TYPE_FOR_EXT_BOWER, 'hasFormTag' => false)); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

