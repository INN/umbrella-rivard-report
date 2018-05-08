<section id="members" class="normal">

	<div class="inn-members-widget">
		<div class="member-wrapper widget-content">
			<ul class="members">
				<?php
				$counter = 1;
				$query = new WP_Query(
					array(
						'post_type' => 'inn_member',
						'posts_per_page' => 500,
						'meta_key'   => '_level',
						'meta_query' => array(
				            array(
				                'key' => '_level',
				                'value' => 40000,
				                'compare' => '>=',
				                'type' => 'numeric'
				            )
				        ),
						'orderby' => array( 
					       '_level'      => 'DESC', 
					       'title' => 'ASC' 
					    ) 
					)
				);
				?>
				<?php if ( $query->have_posts() ) : ?>
					<h4>$100,000 and up</h4>
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

	<div class="inn-members-widget">
		<div class="member-wrapper widget-content">
			<ul class="members">
				<?php
				$counter = 1;
				$query = new WP_Query(
					array(
						'post_type' => 'inn_member',
						'posts_per_page' => 500,
						'meta_key'   => '_level',
						'meta_query' => array(
				            array(
				                'key' => '_level',
				                'value' => 40000,
				                'compare' => '<',
				                'type' => 'numeric'
				            )
				        ),
						'orderby' => array( 
					       '_level'      => 'DESC', 
					       'title' => 'ASC' 
					    ) 
					)
				);
				?>
				<?php if ( $query->have_posts() ) : ?>
					<h4>Up to $100,000</h4>
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
