<?php

include_once get_template_directory() . '/homepages/homepage-class.php';

class RivardReportHome extends Homepage {

	function __construct($options=array()) {
		$suffix = (LARGO_DEBUG)? '' : '.min';

		$defaults = array(
			'name' => __('Rivard Report Homepage Template', 'largo'),
			'type' => 'rrlargo',
			'description' => __('A single big story at the top, with three smaller stories beside it. Banner ad. 5 stories with a sidebar. Resources and guides widget area. Banner ad. Widget area for "The Latest" posts. Widget area for "Multimedia" posts. Widget area for "Featured Series" posts. Widget area for "Topics" posts. Membership info widget area.', 'largo'),
			'template' => get_stylesheet_directory() . '/homepages/templates/rr-homepage.php',
			'assets' => array(
				array( 'homepage-single', get_stylesheet_directory_uri() . '/homepages/assets/css/homepage' . $suffix . '.css', array() ),
			),
			'prominenceTerms' => array(
				array(
					'name' => __('Top Story', 'largo'),
					'description' => __('If you are using a "Big story" homepage layout, add this label to a post to make it the top story on the homepage', 'largo'),
					'slug' => 'top-story'
				)
			)
		);
		$options = array_merge($defaults, $options);
		parent::__construct($options);
	}

	function topStory() {
		global $shown_ids;

		$topstory = largo_home_single_top(); // this handles all the fallbacks required

		$bigStoryPost = largo_home_single_top();
		$shown_ids[] = $bigStoryPost->ID;

		ob_start();

		largo_render_template(
			'partials/rr-featured',
			'top',
			array(
				'bigStoryPost' => $bigStoryPost,
			)
		);

		wp_reset_postdata();
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}


	/**
	 * The four stories underneath the homepage top story
	 */
	function homeFeatured() {
		global $shown_ids, $post;
		ob_start();

		$how_many = 4;

		$featured_stories = largo_home_featured_stories( $how_many );

		if ( count( $featured_stories ) < $how_many ) {
			$recent_stories = wp_get_recent_posts( array(
				'numberposts' => $how_many - count( $featured_stories ),
				'offset' => 0,
				'cat' => 0,
				'orderby' => 'post_date',
				'order' => 'DESC',
				'post__not_in' => array_merge( $shown_ids, wp_list_pluck( $featured_stories, 'ID' ) ),
				'post_type' => 'post',
				'post_status' => 'publish',
			), 'OBJECT');
			$featured_stories = array_merge( $featured_stories, $recent_stories );
		}

		foreach( $featured_stories as $featured ) {
			setup_postdata( $featured->ID );
			$post = $featured;
			$shown_ids[] = $featured->ID;
			$additional = array( 'post-lead' );
			$post_classes = get_post_class( $additional, $featured->ID );

			largo_render_template(
				'partials/rr-featured',
				'image',
				array(
					'post' => $post,
					'post_classes' => $post_classes,
					'featured' => $featured
				)
			);

		}


		$ret = ob_get_contents();
		ob_end_clean();
		wp_reset_postdata();

		return $ret;
	}

	/**
	 * contained within span8, 4 recent posts, excluding Homepage Featured
	 */
	function homeRecent() {
		global $shown_ids, $post;
		ob_start();

		$how_many = 4;

		$featured_stories = largo_home_featured_stories( $how_many );
		$featured_stories = wp_list_pluck( $featured_stories, 'ID' ); // convert this to an array of ids

		$recent_stories = get_posts( array(
			'numberposts' => $how_many,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => 'publish',
			'post__not_in' => array_merge( $featured_stories, $shown_ids ) // exclude Homepage Featured and shown posts
		) );

		foreach( $recent_stories as $recent ) {
			setup_postdata( $recent->ID );
			$post = $recent;
			$shown_ids[] = $recent->ID;
			$additional = array( 'post-lead' );
			$post_classes = get_post_class( $additional, $recent->ID );
		?>
			<div class="rr-recent-smaller">
				<div class="">
					<div class="<?php echo join( ' ', $post_classes ); ?> ">
						<h5 class="top-tag"><?php largo_top_term( array( 'post' => $recent->ID ) ); ?></h5>
						<h3><a href="<?php echo esc_url( get_permalink( $recent->ID ) ); ?>"><?php echo $recent->post_title; ?></a></h3>
						<h5 class="byline"><?php largo_byline( true, true, $recent->ID ); ?></h5>
					</div>
				</div>
			</div>
		<?php
		}


		$ret = ob_get_contents();
		ob_end_clean();
		wp_reset_postdata();

		return $ret;
	}
}

/**
 * Unregister some of the default homepage templates
 * Register our custom one
 *
 * @since 0.1
 */
function rr_custom_homepage_layouts() {
	$unregister = array(
		'HomepageBlog',
		'HomepageSingle',
		'HomepageSingleWithFeatured',
		'HomepageSingleWithSeriesStories',
		'TopStories',
		'LegacyThreeColumn'
	);
	foreach ( $unregister as $layout )
		unregister_homepage_layout( $layout );
	register_homepage_layout( 'RivardReportHome' );
}
add_action( 'init', 'rr_custom_homepage_layouts', 10 );

/**
 * Add RR homepage widget areas
 * This isn't handled with the 'sidebars' index of the $defaults in
 * RivardReportHome::__construct because that only lets us set names,
 * not set wrapping HTML and other things
 */
function rr_add_homepage_widget_areas() {
	$sidebars = array(
		array(
			'name' => __( 'Home Below Top Stories', 'rr' ),
			'id' => 'home-below-top-stories',
			'description'  => __( 'You should put one advertisement widget here.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s span12 clearfix">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="visuallyhidden">',
			'after_title' => '</h3>',
		),
		array(
			'name' => __( 'Home Sidebar', 'rr' ),
			'id' => 'home-sidebar',
			'description' => __( 'This is the right-hand sidebar area on the homepage. Don\'t put more than two widgets here.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s clearfix">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
		),
		array(
			'name' => __( 'Home Resources and Guides', 'rr' ),
			'id' => 'home-resources-and-guides',
			'description' => __( 'This area should have three widgets linking to resources and guides.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s span4">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
		),
		array(
			'name' => __( 'Home Banner Ad Middle', 'rr' ),
			'id' => 'home-banner-ad-middle',
			'description'  => __('You should put one advertisement widget here.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s span12 clearfix">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="visuallyhidden">',
			'after_title' => '</h3>',
		),
		array(
			'name' => __( 'Home "The Latest"', 'rr' ),
			'id' => 'home-the-latest',
			'description' => __( 'This area should be filled with three Largo Recent Posts widgets.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s span12 clearfix">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
		),
		array(
			'name' => __( 'Home "Topics"', 'rr' ),
			'id' => 'home-topics',
			'description' => __( 'This area should be filled with Largo Recent Posts widgets.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s span12 clearfix">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
		),
		array(
			'name' => __( 'Home "Featured Series"', 'rr' ),
			'id' => 'home-featured-series',
			'description' => __( 'This area should be filled with a text widget.', 'rr' )
		),
		array(
			'name' => __( 'Home "Multimedia"', 'rr' ),
			'id' => 'home-multimedia',
			'description' => __( 'This area should be filled with two Largo Recent Posts widgets.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s span12 clearfix">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
		),
		array(
			'name' => __( 'Home Membership Info', 'rr' ),
			'id' => 'home-membership-info',
			'description' => __( 'This area should have three widgets linking to resources and guides.', 'rr' ),
			'before_widget' => '<aside id="%1$s" class="%2$s span4">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
		),
	);

	foreach ( $sidebars as $sidebar ) {
		register_sidebar( $sidebar );
	}
}
add_action( 'widgets_init', 'rr_add_homepage_widget_areas' );
