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
	<a href="<?php echo h('https://github.com/NetCommons3/NetCommons3/tree/' . NC3_VERSION); ?>" target="_blank">
		<?php echo h(NC3_VERSION); ?>
	</a>
	<?php if (NC3_VERSION !== $nc3version) : ?>
		<small>(<?php
			echo NC3_VERSION . '<span class="text-danger"> <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"> </span> ' . $nc3version;
		?></span>)</small>
	<?php endif; ?>
</div>
<?php $this->end();
