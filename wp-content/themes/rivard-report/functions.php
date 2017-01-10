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
// add_action( 'after_setup_theme', 'rivard_report_theme_setup', 11 );

// Enqueue navis css & js
function rivard_report_navis_enqueue() {
	$slides_css = get_template_directory_uri() . '/lib/navis-slideshows/css/slides.css';
	wp_enqueue_style('navis-slides', $slides_css, array(), '1.0');

	$slick_css = get_template_directory_uri() . '/lib/navis-slideshows/vendor/slick/slick.css';
	wp_enqueue_style('navis-slick', $slick_css, array(), '1.0');

	$slides_src = get_template_directory_uri() . '/lib/navis-slideshows/vendor/slick/slick.min.js';
	wp_enqueue_script('jquery-slick', $slides_src, array('jquery'), '3.0', true);

	// our custom js
	$show_src = get_template_directory_uri() . '/lib/navis-slideshows/js/navis-slideshows.js';
	wp_enqueue_script('navis-slideshows', $show_src, array('jquery-slick'), '0.1', true);
}
add_action( 'wp_enqueue_scripts', 'rivard_report_navis_enqueue' );

// Enable navis on all images in post body
function rivard_report_navis_images() {
	echo "<script>jQuery(document).ready(function( $ ) {
			// Loop through each image on the page
			$('article.post img').each(function() {

				// If this is not already a slideshow
				var img = $(this);
			    if ( img[0].hasAttribute('src') ){

			    	// When an image is clicked, add classes to the parent element to open the lightbox view
			    	img.click(function(){
			        	var gallery = img.parent();
				        gallery.addClass('navis-slideshow navis-single navis-full');

				        // Add the close (X) button
				        gallery.prepend('<span class=\"navis-before\">X</span>');

				        // Save original attribute values
				        var sizes = img.attr('sizes'),
				            width = img.attr('width'),
				            height = img.attr('height'),
				            style = img.attr('style');

				        // Adjust styles so images can expand to full width
				        gallery.css('max-width','100%');
				        img.removeAttr('width height sizes');

				        // Close the lightbox when the close (X) button is clicked
				        $('.navis-single .navis-before').click(function(){
				        	$('.navis-before').remove(); // Removes close (X) button
							$('.navis-full').removeClass('navis-full navis-slideshow navis-single'); // Removes navis classes

							// Reset attributes
							img.attr('sizes', sizes);
							img.attr('width', width);
							img.attr('height', height);
							img.attr('style', style);
				        });

				        $(document).keydown(function(e) {
					        if (e.keyCode == 27) { // escape
					            $('.navis-before').remove(); // Removes close (X) button
								$('.navis-full').removeClass('navis-full navis-slideshow navis-single'); // Removes navis classes

								// Reset attributes
								img.attr('sizes', sizes);
								img.attr('width', width);
								img.attr('height', height);
								img.attr('style', style);
					        }
					    });
			        });

			    }
			});
		});</script>";
}
add_action( 'wp_footer', 'rivard_report_navis_images' );
