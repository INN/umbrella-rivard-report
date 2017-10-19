<?php
/**
 * Partial for the large image in the homepage and the category template
 * @var String $thumbnail HTML for the featured image
 * @var WP_Post $bigStoryPost the post
 * @var String $excerpt HTML for the excerpt
 */
$thumbnail = get_the_post_thumbnail( $bigStoryPost->ID, 'rect_thumb' );
$excerpt = largo_excerpt( $bigStoryPost, 1, false, '', false );

	echo '<div '; post_class( 'rr-featured-top', $bigStoryPost->ID ); echo '>';
		if ( ! empty( $thumbnail ) ) {
			?>
				<h5 class="top-tag"><?php largo_top_term( array( 'post' => $bigStoryPost->ID ) ); ?></h5>
				<a href="<?php echo esc_attr( get_permalink( $bigStoryPost->ID ) ); ?>">
					<?php echo $thumbnail; ?>
				</a>
				
				<div class="has-thumbnail">
					<a href="<?php echo esc_attr( get_permalink( $bigStoryPost->ID ) ); ?>" class="clickable"></a>
					<div class="has-thumbnail-inner">
						<h2><a href="<?php echo get_permalink( $bigStoryPost->ID ); ?>"><?php echo $bigStoryPost->post_title; ?></a></h2>
						<section class="excerpt">
							<?php echo $excerpt; ?>
						</section>
						<h5 class="byline"><?php largo_byline( true, true, $bigStoryPost->ID ); ?></h5>
					</div>
				</div>

				
			<?php
		} else {
			?>
				<h5 class="top-tag"><?php largo_top_term(); ?></h5>
				<div class="">
					<h2><a href="<?php echo get_permalink( $bigStoryPost->ID ); ?>"><?php echo $bigStoryPost->post_title; ?></a></h2>
					<section class="excerpt">
						<?php echo $excerpt; ?>
					</section>
				</div>
			<?php
		}
	echo '</div>';
