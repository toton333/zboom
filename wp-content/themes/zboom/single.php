<?php

global $redux_demo;
$column = $redux_demo['opt-layout'];
 get_header( ); ?>
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
					<?php while(have_posts()): the_post(); ?>
					<article>
						<?php the_post_thumbnail(); ?>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title( ); ?></a></h2>
						<div class="info">[By <?php the_author(); ?> on <?php the_time('F j, Y') ?> with <?php comments_popup_link( 'No Comment', '1 Comment', '% Comments', 'munna', 'Comments are disabled' ); ?>]</div>
						<?php the_content(); ?>

						<hr/>
						<?php $prefix = '_zboomMetabox_' ?>
						<p><?php echo get_post_meta( get_the_ID(), $prefix.'text', true ); ?> </p>
						<p>
						<?php $nations = get_post_meta( get_the_ID(), $prefix.'multicheck', true ); 
                           foreach ($nations as $nation) {
                           	echo $nation. '<br/>';
                           }
						?>
					    </p>
					    <p>
					    	<?php
                              cmb2_output_file_list( $prefix.'image', 'small' ); 
					    	 ?>
                             
					    </p>

					    <p>
					    	<?php 
                                 $select = get_post_meta(get_the_ID(), $prefix.'multicheckrepeatable', true );
                                 foreach ($select as $value) {
                                 	foreach ($value as $val) {
                                 		echo $val.'<br/>';
                                 	}
                                 	echo '<hr/>';
                                 }
                                  
					    	?>

					    </p>
					    <p></p>



						<div class="comment">
							Your email address will not be published. Required fields are marked *
							<?php comments_template(); ?>
						</div>
					</article>
				   <?php endwhile; ?>
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