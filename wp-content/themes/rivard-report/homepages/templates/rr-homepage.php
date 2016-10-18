<?php
/**
 * Home Template: Rivard Report Homepage
 * Description: A newspaper-like layout highlighting one Top Story on the left and others to the right. A popular layout choice!
 * Sidebars: Homepage Left Rail (An optional widget area that, when enabled, appears to the left of the main content area on the homepage)
 */

global $largo, $shown_ids, $tags;
?>
<div id="rr-featured" class="row-fluid clearfix">
	<div class="span8">
		<?php echo $topStory; ?>
	</div>
	<div class="span4">
		<div class="widget">
			<?php echo $homeRecent; ?>
		</div>
	</div>
	<div class="row-fluid">
		<?php echo $homeFeatured; ?>
	</div>
</div>
<div id="home-top-banner-ad" class="row-fluid ad-zone">
	<?php if ( !dynamic_sidebar( 'Home Below Top Stories' ) ) { ?>
		<div style="display:block;width:768px;height:90px;margin-left: auto;margin-right: auto;;background-color:#ddd;color:#bb0000; text-align: center;"> Add a 768x90 ad widget to the "Home Below Top Stories" sidebar</div>
	<?php } ?>
</div>
<div id="home-split" class="row-fluid">
	<div class="span8">
		<h2><span><?php _e( 'The Latest', 'rr' ); ?></span></h2>
		<?php //echo $homeSplit; ?>
		<?php if ( !dynamic_sidebar( 'Home "The Latest"' ) ) { ?>
			<aside class="span12" style="display:block;margin-left: auto;margin-right: auto;;background-color:#ddd;color:#bb0000; text-align: center;"> Add some widgets to the Home "The Latest" widget area.</aside>
		<?php } ?>
	</div>

	<div class="span4">
		<?php if ( !dynamic_sidebar( 'Home Sidebar' ) ) { ?>
			<aside style="display:block;margin-left: auto;margin-right: auto;;background-color:#ddd;color:#bb0000; text-align: center;"> Add some widgets to the "Home Sidebar" widget area.</aside>
		<?php } ?>
	</div>
</div>
<div id="home-multimedia">
	<h2><span><?php _e( 'Multimedia', 'rr' ); ?></span></h2>
	<div class="row-fluid">
		<?php if ( !dynamic_sidebar( 'Home "Multimedia"' ) ) { ?>
			<aside class="span12" style="display:block;margin-left: auto;margin-right: auto;;background-color:#ddd;color:#bb0000; text-align: center;"> Add some widgets to the Home "Multimedia" widget area.</aside>
		<?php } ?>
	</div>
</div>
<div id="home-middle-banner-ad" class="row-fluid ad-zone">
	<?php if ( !dynamic_sidebar( 'Home Banner Ad Middle' ) ) { ?>
		<aside style="display:block;width:768px;height:90px;margin-left: auto;margin-right: auto;;background-color:#ddd;color:#bb0000; text-align: center;"> Add a 768x90 ad widget to the "Home Banner Ad Middle" sidebar</aside>
	<?php } ?>
</div>
<div id="home-resources-guides" >
	<h2><span><?php _e( 'Resources and Guides', 'rr' ); ?></span></h2>
	<div class="row-fluid">
		<?php if ( !dynamic_sidebar( 'Home Resources and Guides' ) ) { ?>
			<aside class="span12" style="display:block;margin-left: auto;margin-right: auto;;background-color:#ddd;color:#bb0000; text-align: center;"> Add some widgets to the "Home Resources + Guides" widget area.</aside>
		<?php } ?>
	</div>
</div>
<div id="home-featured-series">
	<h2><span><?php _e( 'Featured', 'rr' ); ?></span></h2>
	<div class="row-fluid">
		<?php if ( !dynamic_sidebar( 'Home "Featured Series"' ) ) { ?>
			<aside class="span12" style="display:block;margin-left: auto;margin-right: auto;;background-color:#ddd;color:#bb0000; text-align: center;"> Add some widgets to the Home "Topics" widget area.</aside>
		<?php } ?>
	</div>
</div>
<div id="home-the-latest">
	<h2><span><?php _e( 'Topics', 'rr' ); ?></span></h2>
	<div class="row-fluid">
		<?php if ( !dynamic_sidebar( 'Home "Topics"' ) ) { ?>
			<aside class="span12" style="display:block;margin-left: auto;margin-right: auto;;background-color:#ddd;color:#bb0000; text-align: center;"> Add some widgets to the Home "Topics" widget area.</aside>
		<?php } ?>
	</div>
</div>
<div id="home-membership-info">
	<h2><span><a href="/donate/">Become A Member Today</a></span></h2>
	<div class="row-fluid">
		<?php if ( !dynamic_sidebar( 'Home Membership Info' ) ) { ?>
			<aside class="span12" style="display:block;margin-left: auto;margin-right: auto;;background-color:#ddd;color:#bb0000; text-align: center;"> Add some widgets to the Home Membership Info widget area.</aside>
		<?php } ?>
	</div>
</div>
