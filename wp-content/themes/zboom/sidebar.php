<?php if(!dynamic_sidebar('right_sidebar' )): ?>
	<div class="box">
		<div class="heading"><h2>Latest Albums</h2></div>
		<div class="content">
			<img src="<?php echo esc_url( get_template_directory_uri().'/images/albums.png'); ?>"/>
		</div>
	</div>
	<div class="box">
		<div class="heading"><h2>Upcoming Events</h2></div>
		<div class="content">
			<div class="list">
				<ul>
					<li><a href="#">Magic Island Ibiza</a></li>
					<li><a href="#">Bamboo Is Just For You</a></li>
					<li><a href="#">Every Hot Summer</a></li>
					<li><a href="#">Magic Island Ibiza</a></li>
					<li><a href="#">Bamboo Is Just For You</a></li>
					<li><a href="#">Every Hot Summer</a></li>
				</ul>
			</div>
		</div>
	</div>
<?php endif; ?>