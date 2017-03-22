<?php
/**
 * Copied from largo/partials/widget-content.php to add some homepage-specific behavior.
 * @since Largo 0.5.5.3
 */

// added: wrap the whole thing in a div with the post class
echo '<div '; post_class(); echo '>';

// The top term
if ( isset( $instance['show_top_term'] ) && $instance['show_top_term'] == 1 && largo_has_categories_or_tags() ) {
	largo_maybe_top_term();
}


// the thumbnail image (if we're using one)
if ($thumb == 'small') {
	$img_location = $instance['image_align'] != '' ? $instance['image_align'] : 'left';
	$img_attr = array('class' => $img_location . '-align');
	$img_attr['class'] .= " attachment-small"; ?>
	<a href="<?php echo get_permalink(); ?>"><?php echo get_the_post_thumbnail( get_the_ID(), '60x60', $img_attr); ?></a>
<?php } elseif ($thumb == 'medium') {
	$img_location = $instance['image_align'] != '' ? $instance['image_align'] : 'left';
	$img_attr = array('class' => $img_location . '-align');
	$img_attr['class'] .= " attachment-thumbnail"; ?>
	<a href="<?php echo get_permalink(); ?>"><?php echo get_the_post_thumbnail( get_the_ID(), 'post-thumbnail', $img_attr); ?></a>
<?php } elseif ($thumb == 'large') {
	$img_attr = array();
	$img_attr['class'] .= " attachment-large"; ?>
	<a href="<?php echo get_permalink(); ?>"><?php echo get_the_post_thumbnail( get_the_ID(), 'large', $img_attr); ?></a>
<?php }

// the headline and optionally the post-type icon
?><h5>
	<a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?>
	<?php
		if ( isset( $instance['show_icon'] ) && $instance['show_icon'] == true ) { 
			post_type_icon();
		}
	?>
	</a>
</h5>

<?php // byline on posts
if ( isset( $instance['show_byline'] ) && $instance['show_byline'] == true) { ?>
	<span class="byline"><?php echo largo_byline( false, $instance['hide_byline_date'] ); ?></span>
<?php }

// Add custom flags for the sponsored and commentary prominences at the bottom of the page
if ( is_home() && $instance['excerpt_display'] === 'none' ) {
	if ( has_term( 'commentary', 'prominence' ) ) {
		$term_link = site_url( '/prominence/commentary/' );
		$term_title = "Commentary";
	}
	if ( has_term( 'sponsored', 'prominence' ) ) {
		$term_link = site_url( '/prominence/sponsored/' );
		$term_title = "Sponsored";
	}

	if ( !empty( $term_link ) && !empty( $term_title ) ) {
		printf(
			'<h5 class="top-tag"><span class="post-category-link"><a href="%1$s" title="Read Posts in the %2$s prominence">%2$s</a></span></h5>',
			$term_link,
			$term_title
		);
	}
}

// the excerpt
if ($excerpt == 'num_sentences') { ?>
	<p><?php echo largo_trim_sentences( get_the_content(), $instance['num_sentences'] ); ?></p>
<?php } elseif ($excerpt == 'custom_excerpt') { ?>
	<p><?php echo get_the_excerpt(); ?></p>
<?php }

// added: close that div
echo '</div>';
