<article id="post-<?php the_ID(); ?>" <?php post_class( 'hnews item in-list' ); ?> itemscope itemtype="http://schema.org/Article">
	<h5 class="top-tag"><?php largo_top_term(); ?></h5>
	<?php
		if ( has_post_thumbnail() ) {
			echo '<a href="', get_permalink(), '">';
			the_post_thumbnail( 'catalyst_featured' );
			echo "</a>";
		}
	?>
	<div class="rr-text-wrap">
		<header>
			<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<h5 class="byline"><?php largo_byline( true, false ); ?></h5>
		</header>
		<?php largo_excerpt( null, 1, FALSE, '' ); ?>
	</div>
</article>
