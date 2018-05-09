<?php
/**
 * A file for functions affecting largo's post-social functions.
 */

/**
 * Add the Linkedin button to the post social buttons directly, not within the "more" menu
 *
 * The 'brute' in this function name is because it uses string splicing and addition functions to add a button.
 *
 * @link http://jira.inn.org/browse/NQ-127A
 * @param string $input the post social links HTML.
 */
function rr_post_social_brute_add_linkedin( $input ) {
	// The start of the "More social links" menu.
	$before = '<span class="more-social-links';

	// Get the postition of the end of $before.
	$position = stripos( $input, $before );

	// Generate the link.
	$link = sprintf(
		'https://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s&summary=%s&source=%s',
		rawurlencode( get_permalink() ),
		rawurlencode( get_the_title() ),
		rawurlencode( wp_strip_all_tags( wp_kses_decode_entities( largo_excerpt( null, 5, false, '', false ) ), true ) ),
		rawurlencode( get_bloginfo( 'name' ) )
	);

	// Insert the link into the Linkedin button.
	$string = sprintf(
		'<span class="linkedin"><a href="%s" target="_blank"><i class="icon-linkedin"></i> <span class="hidden-phone">Share <span class="visuallyhidden">on LinkedIn</span></span></a></span>',
		$link
	);

	// If you link to #disqus_thread, the obvious thing to do, Disqus will replace the inner html of the first instance of that on the page with a comment count.
	// @see rr_fake_comments_link
	$string .= '<span class="comments"><a href="#comments_link"><i class="icon-comment"></i> <span class="hidden-phone">Comments </span></a></span>';

	// Add the button to the largo_post_social_links HTML.
	$output = substr_replace( $input, $string, $position, 0 );

	return $output;
}
add_filter( 'largo_post_social_links', 'rr_post_social_brute_add_linkedin' );

/**
 * Add a link on the page named #comments so we can link to that instead of #disqus_comments, which gets replaced with a comment count by Disqus
 */
function rr_fake_comments_link() {
	echo '<a id="comments_link" class="visually-hidden"></a>';
}
add_action( 'largo_before_comments', 'rr_fake_comments_link' );

/**
 * Putting this back after https://github.com/INN/Largo/issues/1383
 *
 * @since Largo 0.5.5.1
 */
add_action( 'largo_after_hero', 'largo_after_hero_largo_edited_date', 5 );

/**
 * Fix for https://github.com/INN/largo/issues/1474
 *
 * Add the post's top term to the post's post_class array
 *
 * This adds two top-term-derived classes now:
 * - top-term-$taxonomy-$slug
 * - top-term-$term_id
 *
 * @link https://github.com/INN/Largo/issues/1119
 * @since 0.5.5
 * @filter post_class
 * @param array    $classes An array of classes on the post.
 * @param array    $class   An array of additional classes added to the post.
 * @param int|null $post_id The post ID.
 * @return array
 */
function rr_post_class_top_term( $classes = array(), $class = array(), $post_id = null ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_id();
	}

	$top_term_id = get_post_meta( $post_id, 'top_term', true );

	if ( empty( $top_term_id ) ) {
		return;
	}

	// add fallback class in the event that the saved top term
	// is not in one of the taxonomies permitted by the filter
	// largo_top_term_metabox_taxonomies.
	$classes[] = 'top-term-' . $top_term_id;

	// @see largo_top_term_display
	// this array is ordered differently, in favor of
	// in favor of what I'm seeing on a sample of homepages,
	// so that more-likely taxonomies are queried first.
	$taxonomies = apply_filters( 'largo_top_term_metabox_taxonomies', array( 'post_tag', 'category', 'prominence', 'post-type', 'series' ) );

	// @todo: deprecate this because it's a query hog and not a good idea
	foreach ( $taxonomies as $tax ) {
		$term = get_term_by( 'id', $top_term_id, $tax );
		if ( ! empty( $term ) ) {
			// only output the taxonomy-specific .top-term-- if there is a top term in the defined category.
			$classes[] = 'top-term-' . $term->taxonomy . '-' . $term->slug;
			break;
		}
	}

	if ( ! empty( $term ) ) {
		$classes[] = 'top-term-' . $term->taxonomy . '-' . $term->slug;
	}

	return $classes;
}
remove_filter( 'post_class', 'largo_post_class_top_term', 10 );
add_filter( 'post_class', 'rr_post_class_top_term', 10, 3 );
