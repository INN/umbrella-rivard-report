<?php
/**
 * Child Theme for Rivard Report 
 */

// Rivard Report is an INN Member, and should be treated accordingly
define( 'INN_MEMBER', true );


/**
 * Include compiled style.css
 */
function rr_stylesheet() {
	wp_dequeue_style( 'largo-child-styles' );
	$suffix = (LARGO_DEBUG)? '' : '.min';
	wp_enqueue_style( 'rr', get_stylesheet_directory_uri() . '/css/style' . $suffix . '.css' );
}
add_action( 'wp_enqueue_scripts', 'rr_stylesheet', 20 );

/**
 * Include theme files
 *
 * Based off of how Largo loads files: https://github.com/INN/Largo/blob/master/functions.php#L358
 *
 * 1. hook function Largo() on after_setup_theme
 * 2. function Largo() runs Largo::get_instance()
 * 3. Largo::get_instance() runs Largo::require_files()
 *
 * This function is intended to be easily copied between child themes, and for that reason is not prefixed with this child theme's normal prefix.
 *
 * @link https://github.com/INN/Largo/blob/master/functions.php#L145
 */
function largo_child_require_files() {
	$includes = array(
		'/homepages/layouts/RivardReportHomepage.php',
		'/inc/post-tags.php',
		'/inc/taxonomies.php'
	);


	if ( class_exists( 'WP_CLI_Command' ) ) {
		require __DIR__ . '/inc/cli.php';
		WP_CLI::add_command( 'rr', 'RR_WP_CLI' );
	}

	foreach ($includes as $include ) {
		require_once( get_stylesheet_directory() . $include );
	}
}
add_action( 'after_setup_theme', 'largo_child_require_files' );

/**
 * Register sidebars that aren't in the homepage template
 */
function rr_custom_sidebars() {
	$sidebars = array(
		array(
			'name' => __( 'Category Ad Zone', 'rr' ),
			'id' => 'category-ad-zone',
			'description'  => __( 'You should put one advertisement widget here.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s span12 clearfix">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="visuallyhidden">',
			'after_title' => '</h3>',
		),
		array(
			'name' => __( 'Category Sponsorship Zone', 'rr' ),
			'id' => 'category-sponsorship-zone',
			'description'  => __( 'You should put one sponsorship widget here.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s clearfix">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="">',
			'after_title' => '</h3>',
		)
	);

	foreach ( $sidebars as $sidebar ) {
		register_sidebar( $sidebar );
	}
}
add_action( 'widgets_init', 'rr_custom_sidebars' );

/**
 * Placeholder for inserting sponsor/membership messages on archive pages
 * The real requirement is to be able to target this per category so there is likely more work needed here
 */
function rr_interstitial( $counter, $context ) {
	global $wp_query;

	if ( $counter === 3 && is_category() ) {
		$sponsored_id = rr_get_sponsored_posts_in_category( $wp_query->query_vars['category_name'] );

		// If there is a sponsored post in this category, it goes here.
		if ( !empty( $sponsored_id[0] ) ) {
			if ( ! ( $sponsored_id[0] instanceof WP_Post ) ) {
				$sponsored_id[0] = get_post( $sponsored_id[0] );
			}

			// preserve the global $post, set up the sponsored post
			global $post;
			$preserve = $post;
			$post = $sponsored_id[0];

			get_template_part( 'partials/content', 'archive' );

			$post = $preserve;
		// Otherwise, just do the sidebar.
		} else {
			echo '<div class="clearfix ad-zone">';

			if ( !dynamic_sidebar( 'Category Sponsorship Zone' ) ) {
				?>
				<!-- Add some widgets to the "Category Sponsorship Zone" widget area. -->
				<?php
			}

			echo '</div>';
		} // end "If there's a sponsored" conditional
	}
}
add_action( 'largo_loop_after_post_x', 'rr_interstitial', 10, 2 );

// Remove Largo filter that strips links off images in post_content
function rivard_report_theme_setup() {
	remove_filter( 'the_content', 'largo_attachment_image_link_remove_filter' );
}
add_action( 'after_setup_theme', 'rivard_report_theme_setup', 11 );
