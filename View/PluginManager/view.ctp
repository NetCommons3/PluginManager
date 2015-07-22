<?php
/**
 * RssReaders view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->Html->script('/rss_readers/js/rss_readers.js', false); ?>

<div class="nc-content-list" id="nc-rss-readers-<?php echo (int)$frameId; ?>"
		ng-controller="RssReaders"
		ng-init="initialize(<?php echo h(json_encode(['frameId' => $frameId])); ?>)">

	<article>
		<div class="clearfix">
			<div class="pull-left">
				<?php if (isset($rssReader['link'])) : ?>
					<button class="btn btn-default" tooltip="<?php echo __d('rss_readers', 'Site Info'); ?>"
							ng-class="{active:siteInfo}" ng-click="siteInfo = !siteInfo; switchDisplaySiteInfo();">

						<span class="glyphicon glyphicon-info-sign nc-tooltip"> </span>
					</button >

					<?php echo $this->element('NetCommons.status_label',
							array('status' => $rssReader['status'])); ?>
				<?php endif; ?>
			</div>

			<?php if ($contentEditable) : ?>
				<div class="pull-right">
					<span class="nc-tooltip" tooltip="<?php echo __d('net_commons', 'Edit'); ?>">
						<a href="<?php echo $this->Html->url('/rss_readers/rss_readers/edit/' . $frameId) ?>" class="btn btn-primary">
							<span class="glyphicon glyphicon-edit"> </span>
						</a>
					</span>
				</div>
			<?php endif; ?>
		</div>

		<?php echo $this->element('RssReaders/view_site_info'); ?>

		<?php echo $this->element('RssReaders/view_items'); ?>
	</article>
</div>

