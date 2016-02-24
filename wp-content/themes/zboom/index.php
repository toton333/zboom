<?php

global $redux_demo;
$column = $redux_demo['opt-layout'];
 get_header(); ?>

<section id="content">
	<div class="wrap-content zerogrid">
		<div class="row block03">
			<?php if(2 == $column) : ?>
			<div class="col-1-3">
				<div class="wrap-col">
					<?php get_sidebar( ); ?>
				</div>
			</div>
			<?php endif; ?>
			<div class="col-<?php echo (1 == $column)? '3' : '2'; ?>-3">
				<div class="wrap-col">
					<?php if(have_posts()) : while(have_posts()) : the_post(); ?>


					<article>
						<?php the_post_thumbnail(); ?>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						
					    
						<div class="info">[By <?php the_author(); ?> on <?php the_time('F j, Y') ?> with <?php comments_popup_link( 'No Comment', '1 Comment', '% Comments', 'munna', 'Comments are disabled' ); ?> ]</div>
						<p><?php read_more(30); ?></p>
					</article>

					<?php endwhile; else: ?>


					<article>
						<img src="<?php echo get_template_directory_uri(); ?>/images/img1.png"/>
						<h2><a href="#">Dreaming With Us All Night</a></h2>
						<div class="info">[By Admin on December 01, 2012 with <a href="#">01 Commnets</a>]</div>
						<p>Consectetur adipisci. Belit, sed quia non numquam eius modi tempora incidunt, ut labore et dolore ater magnam aliquam quaerat voluptatem. ut enim ad minima ven m, quis nost. Rum exercitationem ullam...</p>
					</article>
					<article>
						<img src="<?php echo get_template_directory_uri(); ?>/images/img2.png"/>
						<h2><a href="#">Welcome To Our Great Site</a></h2>
						<div class="info">[By Admin on December 01, 2012 with <a href="#">01 Commnets</a>]</div>
						<p>Consectetur adipisci. Belit, sed quia non numquam eius modi tempora incidunt, ut labore et dolore ater magnam aliquam quaerat voluptatem. ut enim ad minima ven m, quis nost. Rum exercitationem ullam...</p>
					</article>
                    <?php endif; ?>
                    
                    <?php the_posts_pagination(array('screen_reader_text' => ' ', 'show_all' => true, 'prev_text' => __('Prev', 'zboom')));?>

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