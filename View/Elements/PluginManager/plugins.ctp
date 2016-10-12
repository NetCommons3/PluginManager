<?php
/**
 * PluginManager plugins template
 *   - $pluginType: plugins.type
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php if ($hasFormTag) : ?>
	<?php echo $this->NetCommonsForm->create('Plugins', array(
			'url' => NetCommonsUrl::actionUrlAsArray(array('action' => 'order', $pluginType))
		)); ?>

		<div ng-hide="plugins.type<?php echo $pluginType; ?>.length">
			<p><?php echo __d('net_commons', 'Not found.'); ?></p>
		</div>

		<?php foreach ($pluginsMap['type' . $pluginType] as $key => $value) : ?>
			<?php echo $this->NetCommonsForm->hidden('Plugins.' . $value . '.Plugin.id'); ?>
			<?php echo $this->NetCommonsForm->hidden('Plugins.' . $value . '.Plugin.key'); ?>
			<?php $this->NetCommonsForm->unlockField('Plugins.' . $value . '.Plugin.weight'); ?>
		<?php endforeach; ?>
<?php endif; ?>

	<table class="table table-condensed" ng-show="plugins.type<?php echo $pluginType; ?>.length">
		<thead>
			<tr>
				<th></th>
				<th>
					<?php echo __d('plugin_manager', 'Plugin name'); ?>
				</th>
				<th>
					<?php echo __d('plugin_manager', 'Package'); ?>
				</th>
				<th>
					<?php echo __d('plugin_manager', 'Version'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="plugin in plugins.type<?php echo $pluginType; ?> track by $index">
				<td>
					<?php if ($hasFormTag) : ?>
						<button type="button" class="btn btn-default btn-sm"
								ng-click="move('type<?php echo $pluginType; ?>', 'up', $index)" ng-disabled="($first || sending)">
							<span class="glyphicon glyphicon-arrow-up"></span>
						</button>

						<button type="button" class="btn btn-default btn-sm"
								ng-click="move('type<?php echo $pluginType; ?>', 'down', $index)" ng-disabled="($last || sending)">
							<span class="glyphicon glyphicon-arrow-down"></span>
						</button>

						<input type="hidden" ng-value="{{$index + 1}}"
							   name="data[Plugins][{{getIndex('type<?php echo $pluginType; ?>', plugin.key)}}][Plugin][weight]">
					<?php endif; ?>
				</td>
				<td>
					<a href="" ng-click="showView('<?php echo $pluginType; ?>', plugin.plugin.key)">
						{{plugin.plugin.name}}
					</a>
				</td>
				<td>
					<a target="_blank" ng-href="{{plugin.plugin.packageUrl}}">
						{{plugin.plugin.namespace}}
					</a>
				</td>
				<td>
					<span>
						<a target="_blank" ng-href="{{plugin.plugin.serializeData.commitUrl}}" ng-if="plugin.plugin.serializeData.commitUrl">
							{{plugin.plugin.version}}
							<span class="text-muted">({{plugin.plugin.commitVersion|limitTo:10}})</span>
						</a>
						<span ng-if="!plugin.plugin.serializeData.commitUrl">
							{{plugin.plugin.version}}
							<span class="text-muted">({{plugin.plugin.commitVersion|limitTo:10}})</span>
						</span>
					</span>

					<span class="text-danger" ng-if="plugin.latest && plugin.plugin.commitVersion !== plugin.latest.commitVersion">
						<span class="glyphicon glyphicon-arrow-right" aria-hidden="true"> </span>
						<a target="_blank" ng-href="{{plugin.latest.commitUrl}}" ng-if="plugin.latest.commitUrl">
							{{plugin.latest.version}}
							<span class="text-danger">({{plugin.latest.commitVersion|limitTo:10}})</span>
						</a>
						<span class="text-danger" ng-if="!plugin.latest.commitUrl">
							{{plugin.latest.version}}
							({{plugin.latest.commitVersion|limitTo:10}})
						</span>
					</span>
				</td>
			</tr>
		</tbody>
	</table>

<?php if ($hasFormTag) : ?>
	<div class="text-center">
		<?php echo $this->Button->cancelAndSave(
				__d('net_commons', 'Cancel'),
				__d('net_commons', 'OK'),
				'#',
				array('ng-click' => 'cancel(\'' . NetCommonsUrl::actionUrl(array('action' => 'index', $pluginType), true) . '\')')
			); ?>
	</div>

	<?php echo $this->NetCommonsForm->end(); ?>
<?php endif;
