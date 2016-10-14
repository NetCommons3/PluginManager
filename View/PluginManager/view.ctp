<?php
/**
 * PluginManager view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php $this->start('title_for_modal'); ?>
<?php echo h($plugin['Plugin']['name']); ?>
<?php $this->end(); ?>

<div class="panel panel-default" >
	<div class="panel-body">
		<?php if (Hash::get($plugin, 'Plugin.serialize_data.description')) : ?>
			<div class="plugin-manager-description">
				<?php echo __d(h($plugin['Plugin']['key']), h(Hash::get($plugin, 'Plugin.serialize_data.description'))); ?>
			</div>
		<?php endif; ?>

		<div class="row plugin-manager-list">
			<div class="col-md-2 col-sm-3 col-xs-12">
				<?php echo __d('plugin_manager', 'Package'); ?>
			</div>
			<div class="col-md-10 col-sm-9 col-xs-12">
				<?php if (Hash::get($plugin, 'Plugin.package_url')) : ?>
					<a target="_blank" href="<?php echo h(Hash::get($plugin, 'Plugin.package_url')); ?>">
						<?php echo h($plugin['Plugin']['namespace']); ?>
					</a>
				<?php else: ?>
					<?php echo h($plugin['Plugin']['namespace']); ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="row plugin-manager-list">
			<div class="col-md-2 col-sm-3 col-xs-12">
				<?php echo __d('plugin_manager', 'Version'); ?>
			</div>
			<div class="col-md-10 col-sm-9 col-xs-12 plugin-version">
				<div>
					<?php if (Hash::get($plugin, 'Plugin.serialize_data.commit_url')) : ?>
						<a href="<?php echo Hash::get($plugin, 'Plugin.serialize_data.commit_url'); ?>">
							<?php echo h($plugin['Plugin']['version']); ?>
							<span class="text-muted">
								(<?php echo h($plugin['Plugin']['commit_version']); ?>)
							</span>
						</a>
					<?php else : ?>
						<?php echo h($plugin['Plugin']['version']); ?>
						<span class="text-muted">
							(<?php echo h($plugin['Plugin']['commit_version']); ?>)
						</span>
					<?php endif; ?>
				</div>

				<?php if (Hash::get($plugin, 'latest') &&
							Hash::get($plugin, 'Plugin.commit_version') !== Hash::get($plugin, 'latest.commit_version')) : ?>
					<div class="text-danger">
						<span class="glyphicon glyphicon-arrow-right" aria-hidden="true"> </span>
						<?php if (Hash::get($plugin, 'latest.commit_url')) : ?>
							<a href="<?php echo Hash::get($plugin, 'latest.commit_url'); ?>">
								<?php echo h($plugin['latest']['version']); ?>
								<span class="text-danger">
									(<?php echo h($plugin['latest']['commit_version']); ?>)
								</span>
							</a>
						<?php else : ?>
							<?php echo h($plugin['Plugin']['version']); ?>
							(<?php echo h($plugin['Plugin']['commit_version']); ?>)
						<?php endif; ?>
					</div>
				<?php elseif (! Hash::get($plugin, 'latest')) : ?>
					<div class="text-danger">
						<span class="glyphicon glyphicon-arrow-right" aria-hidden="true"> </span>
						<?php echo __d('net_commons', 'Delete'); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if (Hash::get($plugin, 'Plugin.serialize_data.authors')) : ?>
			<div class="row plugin-manager-list">
				<div class="col-md-2 col-sm-3 col-xs-12">
					<?php echo __d('plugin_manager', 'Author(s)'); ?>
				</div>
				<div class="col-md-10 col-sm-9 col-xs-12">
					<ul class="plugin-manager-autors-ul">
						<?php foreach ((array)Hash::get($plugin, 'Plugin.serialize_data.authors') as $author) : ?>
							<?php
								$name = '';
								if (isset($author['role']) && strtolower($author['role']) === 'developer') {
									$author['name'] = h($author['name']) .
											' <span class="small"><span class="text-success">' .
												__d('plugin_manager', '[Developer]') .
											'</span></span>';
								}
								if (isset($author['homepage'])) {
									$name .= $this->Html->link($author['name'], $author['homepage'], array('target' => '_blank', 'escapeTitle' => false));
								} elseif (isset($author['name'])) {
									$name .= h($author['name']);
								} else {
									$name .= h($author);
								}
							?>
							<li>
								<?php echo $name; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>

		<?php if (Hash::get($plugin, 'Plugin.serialize_data.license')) : ?>
			<div class="row plugin-manager-list">
				<div class="col-md-2 col-sm-3 col-xs-12">
					<?php echo __d('plugin_manager', 'License'); ?>
				</div>
				<div class="col-md-10 col-sm-9 col-xs-12">
					<?php echo implode(', ', (array)Hash::get($plugin, 'Plugin.serialize_data.license')); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if (Hash::get($plugin, 'Plugin.serialize_data.homepage')) : ?>
			<div class="row plugin-manager-list">
				<div class="col-md-2 col-sm-3 col-xs-12 text-nowrap">
					<?php echo __d('plugin_manager', 'Home page'); ?>
				</div>
				<div class="col-md-10 col-sm-9 col-xs-12">
					<a target="_blank" href="<?php echo h(Hash::get($plugin, 'Plugin.serialize_data.homepage')); ?>">
						<?php echo h(Hash::get($plugin, 'Plugin.serialize_data.homepage')); ?>
					</a>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<div class="panel-footer text-center">
		<?php echo $this->NetCommonsForm->create('Plugin', array(
				'url' => NetCommonsUrl::actionUrlAsArray(array('action' => 'edit', $pluginType))
			)); ?>

			<?php echo $this->NetCommonsForm->hidden('Plugin.key'); ?>
			<?php echo $this->Button->save(__d('plugin_manager', 'Update')); ?>

		<?php echo $this->NetCommonsForm->end(); ?>
	</div>
</div>
