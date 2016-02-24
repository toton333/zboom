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

						<?php
						$prefix = '_zboomPageMetabox_';

						$entries = get_post_meta( get_the_ID(), $prefix.'slider', true );

						foreach ( (array) $entries as $key => $entry ) {

						    $img = $title = $desc = $caption = '';

						    if ( isset( $entry['title'] ) )
						        $title = esc_html( $entry['title'] );

						    if ( isset( $entry['description'] ) )
						        $desc = wpautop( $entry['description'] );

						    if ( isset( $entry['image_id'] ) ) {
						        $img = wp_get_attachment_image( $entry['image_id'], 'share-pick', null, array('class' => 'thumb') );
						    }
						    $caption = isset( $entry['caption'] ) ? wpautop( $entry['caption'] ) : '';

						    // Do something with the data
						    
						    echo $title.'<br/>'.$desc.'<br/>'.$img.'<br/>'.$caption.'<br/>';
						    
						}




						?>
					</article>
				   <?php endwhile; ?>
				</div>
			</div>
			<?php if(3 == $column) : ?>
			<div class="col-1-3">
				<div class="wrap-col">
					<?php get_sidebar( ); ?>
					<?php echo  get_post_meta( $post->ID, 'indian', true ); ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>

	<?php 
	/*
	global $redux_demo; 
    foreach ($redux_demo['opt-select'] as $id) {

    	$tag = get_tag($id); // <-- your tag ID
    	echo $tag->name. '<br/>';
    }
   */
	 ?>
</section>
<?php get_footer(); ?>
