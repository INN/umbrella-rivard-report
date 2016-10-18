<?php
/**
 * Functions related to taxonomies
 */

/**
 * Register the 'sponsored' term in the post-prominence taxonomy
 */
function rr_register_sponsored_prominence_term( $terms ) {
	$terms[] = array(
		'name' => __( 'Sponsored', 'rr' ),
		'description' => __( 'Apply this term to sponsored posts.', 'rr' ),
		'slug' => 'sponsored'
	);
	return $terms;
}
add_filter( 'largo_prominence_terms', 'rr_register_sponsored_prominence_term' );

/**
 * Get the most-recent sponsored post for the given category.
 * @see largo_get_featured_posts_in_category
 * @see rr_get_featured_posts_in_category
 */
function rr_get_sponsored_posts_in_category( $category_name, $number = 1 ) {
	$args = array(
		'category_name' => $category_name,
		'numberposts' => $number,
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'prominence',
				'field' => 'slug',
				'terms' => 'sponsored',
			)
		)
	);

	$sponsored_posts = get_posts( $args );
	return $sponsored_posts;
}

/**
 * Get posts marked as "Featured in category" for a given category name.
 *
 * @param string $category_name the category to retrieve featured posts for.
 * @param integer $number total number of posts to return, backfilling with regular posts as necessary.
 * @since Largo 0.5
 * @uses rr_get_featured_posts_in_category
 */
function rr_get_featured_posts_in_category( $category_name, $number = 5 ) {
	// we want to exclude these
	$sponsored_posts = rr_get_sponsored_posts_in_category( $category_name, 1 );
	$sponsored_ids = array_map( function( $x ) { return $x->ID; }, $sponsored_posts );

	$args = array(
		'category_name' => $category_name,
		'numberposts' => $number,
		'post_status' => 'publish',
		'post__not_in' => $sponsored_ids
	);

	$tax_query = array(
		'tax_query' => array(
			array(
				'taxonomy' => 'prominence',
				'field' => 'slug',
				'terms' => 'category-featured',
			)
		)
	);

	// Get the featured posts
	$featured_posts = get_posts( array_merge( $args, $tax_query ) );

	// Backfill with regular posts if necessary
	if ( count( $featured_posts ) < (int) $number ) {

		// backfill should not be in the sponsored ids or in the featured ids
		$featured_ids = array_map( function( $x ) { return $x->ID; }, $featured_posts );
		$backfill_exclude_ids = array_merge( $featured_ids, $sponsored_ids );

		$needed = (int) $number - count( $featured_posts );
		$regular_posts = get_posts( array_merge( $args, array(
			'numberposts' => $needed,
			'post__not_in' => $backfill_exclude_ids
		)));
		$featured_posts = array_merge( $featured_posts, $regular_posts );
	}

	return $featured_posts;
}

/**
 * Exclude the first sponsored post from a category's main query
 * @uses rr_get_sponsored_posts_in_category
 * @uses rr_get_featured_posts_in_category
 * @see largo_category_archive_posts
 */
function rr_exclude_sponsored_posts_in_category( $query ) {
	// don't muck with admin, non-categories, etc
	if ( ! $query->is_category() || ! $query->is_main_query() || is_admin() ) return;

	// get the sponsored posts
	$sponsored_posts = rr_get_sponsored_posts_in_category( $query->get( 'category_name' ), 1 );

	// Get the ids from the sponsored posts
	$sponsored_post_ids = array();
	foreach ( $sponsored_posts as $sponsored_post ) {
		$sponsored_post_ids[] = $sponsored_post->ID;
	}

	// This is copied from largo_category_archive_posts
	// If this has been disabled by an option, do nothing
	$featured_post_ids = array();
	if ( ! of_get_option( 'hide_category_featured' ) == true ) {
		// get the featured posts
		// If we don't want sponsored posts here, we're going to need to modify this function and category.php
		$featured_posts = rr_get_featured_posts_in_category( $query->get( 'category_name' ) );

		// get the IDs from the featured posts
		foreach ( $featured_posts as $fpost ) {
			$featured_post_ids[] = $fpost->ID;
		}
	}

	$ids = array_merge( $featured_post_ids, $sponsored_post_ids );

	$query->set( 'post__not_in', $ids );

}
add_action( 'pre_get_posts', 'rr_exclude_sponsored_posts_in_category', 15, 1 );

/**
 * function to unregister the pre_get_posts hook in largo
 * This is because rr_exclude_sponsored_posts_in_category conflicts with largo_category_archive_posts
 * Both modify the main query, causing is_main_query() to return false, preventing the other from working.
 *
 * @see rr_exclude_sponsored_post_in_category
 */
function rr_unregister_largo_category_archive_posts() {
	remove_action( 'pre_get_posts', 'largo_category_archive_posts', 15 );
}
add_action( 'init', 'rr_unregister_largo_category_archive_posts' );
