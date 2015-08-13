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

echo $this->Html->css(
	array(
		'/plugin_manager/css/style.css'
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

<div class="panel panel-default" >
	<div class="panel-body">
		<h2 class="nc-title-in-panel">
			<strong><?php echo h($plugin['Plugin']['name']); ?></strong>
		</h2>
		<?php if ($plugin['composer']) : ?>
			<div class="plugin-manager-description">
				<?php echo __d(h($plugin['Plugin']['key']), h($plugin['composer']['description'])); ?>
			</div>
		<?php endif; ?>

		<?php if ($plugin['composer']) : ?>
			<div class="row plugin-manager-list">
				<div class="col-md-2 col-sm-3 col-xs-12">
					<?php echo __d('plugin_manager', 'Package'); ?>
				</div>
				<div class="col-md-10 col-sm-9 col-xs-12">
					<a target="_blank" href="<?php echo Plugin::PACKAGIST_URL . h($plugin['composer']['name']); ?>">
						<?php echo h($plugin['composer']['name']); ?>
					</a>
				</div>
			</div>
		<?php endif; ?>

		<?php if (isset($plugin['composer']['source'])) : ?>
			<div class="row plugin-manager-list">
				<div class="col-md-2 col-sm-3 col-xs-12">
					<?php echo __d('plugin_manager', 'Version'); ?>
				</div>
				<div class="col-md-10 col-sm-9 col-xs-12">
					<?php echo h($plugin['composer']['version']); ?>
					<span class="text-muted">(<?php echo h($plugin['composer']['source']['reference']); ?>)</span>
				</div>
			</div>

			<div class="row plugin-manager-list">
				<div class="col-md-2 col-sm-3 col-xs-12">
					<?php echo __d('plugin_manager', 'Source'); ?>
				</div>
				<div class="col-md-10 col-sm-9 col-xs-12">
					<a target="_blank" href="<?php echo h($plugin['composer']['source']['url']); ?>">
						<?php echo h($plugin['composer']['source']['url']); ?>
					</a>
				</div>
			</div>
		<?php endif; ?>

		<?php if (isset($plugin['composer']['authors'])) : ?>
			<div class="row plugin-manager-list">
				<div class="col-md-2 col-sm-3 col-xs-12">
					<?php echo __d('plugin_manager', 'Author(s)'); ?>
				</div>
				<div class="col-md-10 col-sm-9 col-xs-12">
					<ul class="plugin-manager-autors-ul list-inline">
						<?php foreach ($plugin['composer']['authors'] as $author) : ?>
							<?php
								$name = '';
								if (isset($author['role']) && strtolower($author['role']) === 'developer') {
									$author['name'] = h($author['name']) .
											' <span class="small"><span class="text-danger">' .
												__d('plugin_manager', '[Developer]') .
											'</span></span>';
								}
								if (isset($author['homepage'])) {
									$name .= $this->Html->link($author['name'], $author['homepage'], array('target' => '_blank', 'escapeTitle' => false));
								} else {
									$name .= h($author['name']);
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

		<?php if (isset($plugin['composer']['license'])) : ?>
			<div class="row plugin-manager-list">
				<div class="col-md-2 col-sm-3 col-xs-12">
					<?php echo __d('plugin_manager', 'License'); ?>
				</div>
				<div class="col-md-10 col-sm-9 col-xs-12">
					<?php echo implode(', ', $plugin['composer']['license']); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if (isset($plugin['composer']['homepage'])) : ?>
			<div class="row plugin-manager-list">
				<div class="col-md-2 col-sm-3 col-xs-12">
					<?php echo __d('plugin_manager', 'Home page'); ?>
				</div>
				<div class="col-md-10 col-sm-9 col-xs-12">
					<a target="_blank" href="<?php echo h($plugin['composer']['homepage']); ?>">
						<?php echo h($plugin['composer']['homepage']); ?>
					</a>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php echo $this->element('PluginManager/btn_form'); ?>
</div>
