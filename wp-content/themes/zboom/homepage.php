<?php 
/*
Template Name: Homepage Template
*/
global $redux_demo;
$column = $redux_demo['opt-layout'];
get_header( );
?>
<div class="featured">
	<div class="wrap-featured zerogrid">
		<div class="slider">
			<div class="rslides_container">
				<ul class="rslides" id="slider">
					<?php
                     $args = array('post_type' => 'slider');
                     $slider = new WP_Query($args);
					 if($slider->have_posts()) : while($slider->have_posts()): $slider->the_post(); ?>
                     <li><?php the_post_thumbnail(); ?></li>
					<?php endwhile; else:  ?>
					<li><img src="<?php echo get_template_directory_uri();  ?>/images/slide1.png"></li>
					<li><img src="<?php echo get_template_directory_uri();  ?>/images/slide2.png"></li>
					<li><img src="<?php echo get_template_directory_uri();  ?>/images/slide3.png"></li>
				    <?php endif; wp_reset_postdata(); ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<!--Content-->
<section id="content">
	<div class="wrap-content zerogrid">
		<div class="row block01">
            <?php

            $args = array('post_type' => 'block', 'posts_per_page' =>3);
            $blocks = new WP_Query($args);
            if($blocks->have_posts()): while($blocks->have_posts()): $blocks->the_post();

             ?>

             <div class="col-1-3">
				<div class="wrap-col box">
					<h2><?php the_title( ); ?></h2>
					<p><?php read_more(20); ?></p>
					<div class="more"><a href="<?php the_permalink(); ?>">[...]</a></div>
				</div>
			</div>

           <?php endwhile; endif; wp_reset_postdata();?>
		</div>
		<div class="row block02">
			<?php if(2 == $column) : ?>
			<div class="col-1-3">
				<div class="wrap-col">
					<?php get_sidebar( ); ?>
				</div>
			</div>
			<?php endif; ?>
			<div class="col-<?php echo (1 == $column)? '3' : '2'; ?>-3">
				<div class="wrap-col">
					<div class="heading"><h2>Latest Blog</h2></div>
					<?php
                      $args = array('post_type' => 'post', 'posts_per_page' => 5);
                      $posts = new WP_Query($args);
					 if($posts->have_posts()): while($posts->have_posts()) : $posts->the_post();  ?>
					<article class="row">
						<div class="col-1-3">
							<div class="wrap-col">
								<?php the_post_thumbnail( ); ?>
							</div>
						</div>
						<div class="col-2-3">
							<div class="wrap-col">
								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<div class="info">By <?php the_author( ); ?> on <?php the_time('F d, Y' ); ?>  <?php comments_popup_link( 'No Comment', 'with 1 Comment', 'with % Comments', 'munna', '' ); ?></div>
								<p><?php read_more(20) ?> [...]</p>
							</div>
						</div>
					</article>

					<?php endwhile; endif; wp_reset_postdata(); ?>
				</div>


			</div>
			<?php if(3 == $column) : ?>
			<div class="col-1-3">
				<div class="wrap-col">
					<?php get_sidebar( ); ?>
				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</section>
<?php get_footer(); ?>