<!DOCTYPE html>
<!--[if lt IE 7]> <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7]>    <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8]>    <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if IE 9]>    <html <?php language_attributes(); ?> class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<?php
/**
 * Clean Page template
 * Template Name: Clean Page (No sidebars or ads)
 * Description: Shows the post but does not load any sidebars.
 *
 * Based off of Largo's page.php, but including:
 * - Largo's partials/content-page
 * - Largo's header.php, modified to remove ads and navigations
 * - Largo's partials/header.php, removing actions
 * - Largo's footer, removing ads and keeping navigation

 * @link jira.inn.org/browse/RNS-71
 */

global $shown_ids;

add_filter( 'body_class', function( $classes ) {
	$classes[] = 'normal';
	return $classes;
} );

?>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php
	wp_head();
?>
<body <?php body_class(); ?>>
	<div id="top"></div>

	<?php

	/**
	 * Fires at the top of the page, just after the id=top DIV element.
	 *
	 * @since 0.4
	 */
	do_action( 'largo_top' );

	?>

	<?php
			//get_template_part( 'partials/nav', 'global' );
	?>

	<div id="page" class="hfeed clearfix">

		<?php
			echo '<a itemprop="url" class="alignleft visible-desktop" id="page-clean-gohome" href="' . esc_url( home_url( '/' ) ) . '">';
			echo __( 'Back to', 'largo' ) . ' ';
			bloginfo( 'name' ); // Add the blog name.
			echo '</a>';
		?>

		<?php
			/**
			 * Largo/partials/largo-header.php
			 *
			 * Removed: the actions.
			 *
			 * @since Largo 0.5.4
			 */
		?>
		<header id="" class="clearfix nocontent text-align-center" itemscope itemtype="http://schema.org/Organization">
			<?php
				echo '<a itemprop="url" href="' . esc_url( home_url( '/' ) ) . '"><img class="header_img" src="' . of_get_option('banner_image_med') . '" alt="" /></a>';
			?>
		</header>

		<div id="main" class="row-fluid clearfix">

		<?php

		/**
		 * Fires at the top of the Largo id=main DIV element.
		 *
		 * @since 0.4
		 */
		do_action( 'largo_main_top' );

/*
 * The actual meat of the page
 */
?>
<div id="content" role="main">
	<?php
		while ( have_posts() ) : the_post();

			$shown_ids[] = get_the_ID();

			get_template_part( 'partials/content-page-clean' );

		endwhile;
	?>
</div>

<?php do_action( 'largo_after_content' ); ?>

<?php

/**
 * Here begins Largo/footer.php
 *
 * Removed: before-footer sidebar, largo_before_footer action
 */
?>
	</div> <!-- #main -->
</div><!-- #page -->
<div class="footer-bg clearfix nocontent">
	<footer id="site-footer">

		<?php
			get_template_part( 'partials/footer', 'widget-area' );

			get_template_part( 'partials/footer', 'boilerplate' );

		    /**
		     * Fires just before the Largo footer content ends.
		     *
		     * @since 0.4
		     */
			do_action( 'largo_before_footer_close' );
		?>

	</footer>
</div>

<?php
	/**
	 * Fires after the Largo footer content.
	 *
	 * @since 0.4
	 */
	do_action( 'largo_after_footer' );

	wp_footer();
?>
</body>
</html>
<?php
