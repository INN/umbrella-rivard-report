<?php
/**
 * Template for the featured not-top stories
 * @var Array $post_classes of string
 * @var WP_Post $featured
 */

$thumbnail = get_the_post_thumbnail( $featured->ID, 'rect_thumb' );
?>
	<div <?php post_class( 'rr-featured-smaller', $featured->ID ); ?>>
		<div class="">
			<div class="<?php echo join( ' ', $post_classes ); ?> ">

				<h5 class="top-tag"><?php largo_top_term( array( 'post' => $featured->ID ) ); ?></h5>
				<h3><a href="<?php echo esc_url( get_permalink( $featured->ID ) ); ?>"><?php echo $featured->post_title; ?></a></h3>
				<h5 class="byline"><?php largo_byline( true, true, $featured->ID ); ?></h5>
			</div>
		</div>
	</div>
