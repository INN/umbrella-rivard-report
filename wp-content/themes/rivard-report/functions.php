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
 * Replace Largo's sticky navigation JS with one that doesn't hide on scroll
 * @link https://github.com/INN/largo/blob/v0.5.5.3/inc/enqueue.php#L35-L42
 */
function rr_navigation_js() {
	wp_dequeue_script( 'largo-navigation' );
	wp_enqueue_script(
		'rr-navigation',
		get_stylesheet_directory_uri(). '/js/navigation.js',
		array( 'largoCore' ),
		largo_version(),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'rr_navigation_js', 20 );

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
 * Register sidebars, one for each category
 * Create widgetized sidebars for each category
 *
 * This function is attached to the 'widgets_init' action hook.
 *
 * @uses	register_sidebar()
 * @uses	get_categories()
 * @uses	get_cat_name()
 * @link	https://bavotasan.com/2012/create-widgetized-sidebars-for-each-category-in-wordpress/
 */
function rr_category_sidebars() {
	$categories = get_categories( array( 'hide_empty'=> 0 ) );

	foreach ( $categories as $category ) {
		if ( 0 == $category->parent )
			register_sidebar( array(
				'name' => $category->cat_name,
				'id' => $category->category_nicename . '-sidebar',
				'description' => 'This is the ' . $category->cat_name . ' category header advertisement area',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			) );
	}
}
add_action( 'widgets_init', 'rr_category_sidebars', 40 ); // 40 to put this below every other widget

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

/**
 * Enqueue Js to modify the behavior of Popmake
 */
function rivard_popmake_js() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'popup-maker/popup-maker.php' ) ) {
		wp_enqueue_script(
			'rr-popmake',
			get_stylesheet_directory_uri(). '/js/popmake.js',
			array( 'jquery', 'popup-maker-site' ), // depends upon both of these
			null,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'rivard_popmake_js' );
