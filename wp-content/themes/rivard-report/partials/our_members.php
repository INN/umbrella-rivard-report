<section id="members" class="normal">
	<h3><a href="/members/"><span>$150,000</span></a></h3>

	<div class="inn-members-widget">
		<div class="member-wrapper widget-content">
			<ul class="members">
				<?php
				$counter = 1;
				$query = new WP_Query(
					array(
						'post_type' => 'inn_member',
						'posts_per_page' => 500,
						'order' => 'ASC',
						'orderby' => 'title',
						'meta_key'   => '_level',
						'meta_value'  => 25000
					)
				);
				?>
				<?php if ( $query->have_posts() ) : ?>
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php setup_postdata( $post ); ?>
						<li id="member-list-<?php echo $post->ID;?>">
							<a href="<?php echo get_post_meta( $post->ID, '_url', true ); ?>" class="member-thumb" title="<?php echo get_the_title(); ?>">
								<?php the_post_thumbnail('medium'); ?>
							</a>
						</li>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<h3><a href="/members/"><span>$100,000</span></a></h3>

	<div class="inn-members-widget">
		<div class="member-wrapper widget-content">
			<ul class="members">
				<?php
				$counter = 1;
				$query = new WP_Query(
					array(
						'post_type' => 'inn_member',
						'posts_per_page' => 500,
						'order' => 'ASC',
						'orderby' => 'title',
						'meta_key'   => '_level',
						'meta_value'  => 50000
					)
				);
				?>
				<?php if ( $query->have_posts() ) : ?>
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php setup_postdata( $post ); ?>
						<li id="member-list-<?php echo $post->ID;?>">
							<a href="<?php echo get_post_meta( $post->ID, '_url', true ); ?>" class="member-thumb" title="<?php echo get_the_title(); ?>">
								<?php the_post_thumbnail('medium'); ?>
							</a>
						</li>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>




</section>
