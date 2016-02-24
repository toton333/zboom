<?php
/*
Template Name: contact Page
*/
 get_header( ); ?>

<section id="content">
	<div class="wrap-content zerogrid">
		<div class="row block03">
			<div class="col-2-3">
				<div class="wrap-col">
					<article>
						<div class="comment">
							Your email address will not be published. Required fields are marked *
							<?php while(have_posts()) : the_post(); ?>
							 <?php the_content(); ?>
						    <?php endwhile; ?>
						</div>
					</article>
				</div>
			</div>
			<div class="col-1-3">
				<div class="wrap-col">
					<?php if(!dynamic_sidebar('contact_sidebar' )): ?>
					<div class="box">
						<div class="heading"><h2>Find Us</h2></div>
						<div class="content">
							<img src="<?php echo esc_url( get_template_directory_uri().'/images/map.png' ); ?>"/>
							<p>Petru Zadnipru 15/2 Chisinau 2044, Republic of Moldova</p>
							<p>Freephone : +1 800 445 7880</p>
							<p>Telephone : +1 800 995 6880</p>
							<p>Fax : +1 800 465 1990</p>
							<p>Email : zerotheme@demolink.com</p>
						</div>
					</div>
				    <?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer( ); ?>