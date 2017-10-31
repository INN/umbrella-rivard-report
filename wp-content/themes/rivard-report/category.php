<?php
/**
 * Template for category archive pages
 *
 * @package Largo
 * @since 0.4
 * @filter largo_partial_by_post_type
 *
 * @uses rr_get_featured_posts_in_category
 * @see rr_unregister_largo_category_archive_posts
 * @see rr_interstitial
 */
get_header();

global $tags, $paged, $post, $shown_ids;

$title = single_cat_title( '', false );
$description = category_description();
$rss_link = get_category_feed_link( get_queried_object_id() );
$posts_term = of_get_option( 'posts_term_plural', 'Stories' );
$queried_object = get_queried_object();
?>

<div class="clearfix">
	<header class="archive-background clearfix">
		<a class="rss-link rss-subscribe-link" href="<?php echo $rss_link; ?>"><?php echo __( 'Subscribe', 'largo' ); ?> <i class="icon-rss"></i></a>
		<?php
			$post_id = largo_get_term_meta_post( $queried_object->taxonomy, $queried_object->term_id );
			largo_hero( $post_id );
		?>
		<h1 class="page-title"><?php echo $title; ?></h1>
		<div class="archive-description"><?php echo $description; ?></div>
		<?php do_action( 'largo_category_after_description_in_header' ); ?>
	</header>

	<section id="cat-sponsored" class="clearfix">
	<?php
		/**
		 * Output per-category sidebar here
		 *
		 * @link https://bavotasan.com/2012/create-widgetized-sidebars-for-each-category-in-wordpress/
		 */
		$sidebar_id = ( is_category() ) ? sanitize_title( get_cat_name( get_query_var( 'cat' ) ) ) . '-sidebar' : 'sidebar';
		if ( is_active_sidebar( $sidebar_id ) ) {
			echo '<h6 class="clearfix by">Presented by</h6>';
			echo '<div class="items">';
			dynamic_sidebar( $sidebar_id );
			echo '</div>';
		}
	?>
	</section>

	<?php
	if ( $paged < 2 && of_get_option( 'hide_category_featured' ) == '0' ) {
		$featured_posts = rr_get_featured_posts_in_category( $wp_query->query_vars['category_name'] );
		if ( count( $featured_posts ) > 0 ) {
			$top_featured = $featured_posts[0];
			$shown_ids[] = $top_featured->ID; ?>

			<div id="rr-featured" class="row-fluid">
				<div class="span8">
				<?php
					largo_render_template(
						'partials/rr-featured',
						'top',
						array(
							'bigStoryPost' => $top_featured,
							'post_classes' => get_post_class( array( 'post-lead' ), $top_featured->ID ),
							'featured' => $top_featured
						)
					);
				?>
				</div>
			<div class="span4">

			<?php
				$secondary_featured = array_slice( $featured_posts, 1 );
				if ( count( $secondary_featured ) > 0 ) {
					foreach ( $secondary_featured as $idx => $featured ) {
						$shown_ids[] = $featured->ID;
						$thumbnail = get_the_post_thumbnail( $featured->ID, 'rect_thumb' );
						$post_classes = get_post_class( $additional, $recent->ID );
						?>
						<div class="rr-featured-smaller">
							<div class="">
								<div class="<?php echo join( ' ', $post_classes ); ?> ">

									<h5 class="top-tag"><?php largo_top_term( array( 'post' => $featured->ID ) ); ?></h5>
									<h3><a href="<?php echo esc_url( get_permalink( $featured->ID ) ); ?>"><?php echo $featured->post_title; ?></a></h3>
									<h5 class="byline"><?php largo_byline( true, true, $featured->ID ); ?></h5>
								</div>
							</div>
						</div>
						<?php
					}
				} ?>
				</div>
			</div>
		<?php }
	}
	?>
</div>

<div id="category-ad-zone" class="ad-zone row-fluid">
	<?php if ( ! dynamic_sidebar( 'Category Ad Zone' ) ) { ?>
		<aside style="display:block;width:768px;height:90px;margin-left: auto; margin-right: auto;background-color:#ddd;color:#bb0000; text-align: center;"> Add a 768x90 ad widget to the "Home Below Top Stories" sidebar</aside>
	<?php } ?>
</div>

<?php
/*
 * here go the children categories of the category
 * each should be span4 button
 * may be n many, needs to collapse to 2 and 1 columns as appropriate
 */
	$children = get_categories( array(
		'parent' => $queried_object->cat_ID
	) );

	// only output this area if there are child categories
	if ( count($children) > 0 ) {
	?>

		<div id="category-children">
			<h5 class="heading-treatment"><?php _e( 'Topics', 'rr' ); ?></h5>
			<div class="row-fluid">

				<?php
				/** Create the buttons */
				foreach ( $children as $child ) {
					printf(
						'<button class="span4"><a href="%1$s">%2$s</a></button>',
						get_category_link($child->cat_ID),
						$child->name
					);
				}
				?>

			</div>
		</div>

	<?php
	}
?>

<div class="row-fluid clearfix">
	<div class="stories span8" role="main" id="content">
		<h2 class="heading-treatment"><span><?php _e( 'The Latest', 'rr' ); ?></span></h2>
		<?php
			do_action( 'largo_before_category_river' );
			if ( have_posts() ) {
				$counter = 1;
				while ( have_posts() ) {
					the_post();
					$post_type = get_post_type();
					$partial = largo_get_partial_by_post_type( 'archive', $post_type, 'archive' );
					get_template_part( 'partials/content', 'archive' );
					do_action( 'largo_loop_after_post_x', $counter, $context = 'archive' );
					$counter++;
				}
				largo_content_nav( 'nav-below' );
			} else {
				get_template_part( 'partials/content', 'not-found' );
			}
			do_action( 'largo_after_category_river' );
		?>
	</div>
	<?php get_sidebar(); ?>
</div>

<?php
get_footer();
