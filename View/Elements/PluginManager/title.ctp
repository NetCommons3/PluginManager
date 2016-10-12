<?php
/**
 * Sub title template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php $this->start('subtitle'); ?>
<div class="pull-right">
	<?php echo __d('plugin_manager', 'NetCommons3: '); ?>
	<a href="<?php echo h(Hash::get($nc3plugin, 'Plugin.serialize_data.commit_url', '')); ?>" target="_blank">
		<?php echo h($nc3plugin['Plugin']['version']); ?>
		<small><span class="text-muted">(<?php echo h(substr($nc3plugin['Plugin']['commit_version'], 0, 10)); ?>)</span></small>
	</a>
</div>
<?php $this->end();
