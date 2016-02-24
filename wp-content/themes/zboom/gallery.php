<?php 
/*
 Template Name: Gallery Template

*/
get_header();
 ?>
<section id="content">
	<div class="wrap-content zerogrid">
		<div class="row block03">

			<?php
                  $args = array('post_type' => 'gallery', 'posts_per_page' => -1);
                  $gallery = new WP_Query($args);

                  if($gallery->have_posts()) : while($gallery->have_posts()) : $gallery->the_post();

			 ?>
			<div class="col-1-4">
				<div class="wrap-col">
					<article>
						<?php the_post_thumbnail( ); ?>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title( ); ?></a></h2>
					</article>
				</div>
			</div>
			<?php endwhile; endif; wp_reset_postdata(); ?>
		</div>
		
	</div>
</section>
<?php get_footer( ); ?>