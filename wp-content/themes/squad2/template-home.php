<?php 
 /*
 Template Name: Front Page Template
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> >

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">



 <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> id="page-top" data-spy="scroll" data-target=".navbar-custom">
	<!-- Preloader -->
	<div id="preloader">
	  <div id="load"></div>
	</div>

    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="index.html">
                    <h1>SQUAD FREE</h1>
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                     <ul class="nav navbar-nav">
                       <?php
                       global $post;

                       $args = array('post_type' => 'page', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC');
                       $posts = get_posts( $args );        
                     
                        foreach($posts as $post) : setup_postdata( $post );
                        ?>
                     <li><a href="#<?php echo $post->post_name; ?>"><?php the_title(); ?></a></li>
                       <?php endforeach; wp_reset_postdata()?>
                     </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>



	<!-- Section: intro -->
    <section id="intro" class="intro">
	
		<div class="slogan">
			<h2>WELCOME TO <span class="text_color">SQUAD</span> </h2>
			<h4>WE ARE GROUP OF GENTLEMEN MAKING AWESOME WEB WITH BOOTSTRAP</h4>
		</div>
		<div class="page-scroll">
			<a href="#service" class="btn btn-circle">
				<i class="fa fa-angle-double-down animated"></i>
			</a>
		</div>
    </section>
	<!-- /Section: intro -->
  <?php 
  global $post;
  $args = array('post_type' => 'page', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC',
               'post__not_in' => array(14,49)  );
  $posts = get_posts( $args );
  foreach($posts as $post) : setup_postdata( $post);



   ?>
   	<!-- Section: about -->
       <section id="<?php echo $post->post_name; ?>" class="home-section text-center">
   		<div class="heading-about">
   			<div class="container">
   			<div class="row">
   				<div class="col-lg-8 col-lg-offset-2">
   					<div class="wow bounceInDown" data-wow-delay="0.4s">
   					<div class="section-heading">
   					<h2><?php the_title( ); ?></h2>
   					<i class="fa fa-2x fa-angle-down"></i>

   					</div>
   					</div>
   				</div>
   			</div>
   			</div>
   		</div>
   		<div class="container">

   		<div class="row">
   			<div class="col-lg-2 col-lg-offset-5">
   				<hr class="marginbot-50">
   			</div>
   		</div>
           <div class="row">
               <div class="col-xs-6 col-sm-3 col-md-3">
   				<div class="wow bounceInUp" data-wow-delay="0.2s">
                   <div class="team boxed-grey">
                       <div class="inner">
   						<h5>Anna Hanaceck</h5>
                           <p class="subtitle">Pixel Crafter</p>
                           <div class="avatar"><img src="<?php echo get_template_directory_uri(); ?>/img/team/1.jpg" alt="" class="img-responsive img-circle" /></div>
                       </div>
                   </div>
   				</div>
               </div>
   			<div class="col-xs-6 col-sm-3 col-md-3">
   				<div class="wow bounceInUp" data-wow-delay="0.5s">
                   <div class="team boxed-grey">
                       <div class="inner">
   						<h5>Maura Daniels</h5>
                           <p class="subtitle">Ruby on Rails</p>
                           <div class="avatar"><img src="<?php echo get_template_directory_uri(); ?>/img/team/2.jpg" alt="" class="img-responsive img-circle" /></div>

                       </div>
                   </div>
   				</div>
               </div>
   			<div class="col-xs-6 col-sm-3 col-md-3">
   				<div class="wow bounceInUp" data-wow-delay="0.8s">
                   <div class="team boxed-grey">
                       <div class="inner">
   						<h5>Jack Briane</h5>
                           <p class="subtitle">jQuery Ninja</p>
                           <div class="avatar"><img src="<?php echo get_template_directory_uri(); ?>/img/team/3.jpg" alt="" class="img-responsive img-circle" /></div>

                       </div>
                   </div>
   				</div>
               </div>
   			<div class="col-xs-6 col-sm-3 col-md-3">
   				<div class="wow bounceInUp" data-wow-delay="1s">
                   <div class="team boxed-grey">
                       <div class="inner">
   						<h5>Tom Petterson</h5>
                           <p class="subtitle">Typographer</p>
                           <div class="avatar"><img src="<?php echo get_template_directory_uri(); ?>/img/team/4.jpg" alt="" class="img-responsive img-circle" /></div>

                       </div>
                   </div>
   				</div>
               </div>
           </div>		
   		</div>
   	</section>
   	<!-- /Section: about -->
   

   
<?php endforeach;  wp_reset_postdata();?>

	 	
	<?php echo 'LOOPS END HERE'; ?>

	<!-- Section: services -->
    <section id="service" class="home-section text-center bg-gray">
		
		<div class="heading-about">
			<div class="container">
			<div class="row">
				<div class="col-lg-8 col-lg-offset-2">
					<div class="wow bounceInDown" data-wow-delay="0.4s">
					<div class="section-heading">
					<h2>Our Services</h2>
					<i class="fa fa-2x fa-angle-down"></i>

					</div>
					</div>
				</div>
			</div>
			</div>
		</div>
		<div class="container">
		<div class="row">
			<div class="col-lg-2 col-lg-offset-5">
				<hr class="marginbot-50">
			</div>
		</div>
        <div class="row">
            <div class="col-sm-3 col-md-3">
				<div class="wow fadeInLeft" data-wow-delay="0.2s">
                <div class="service-box">
					<div class="service-icon">
						<img src="<?php echo get_template_directory_uri(); ?>/img/icons/service-icon-1.png" alt="" />
					</div>
					<div class="service-desc">
						<h5>Print</h5>
						<p>Vestibulum tincidunt enim in pharetra malesuada. Duis semper magna metus electram accommodare.</p>
					</div>
                </div>
				</div>
            </div>
			<div class="col-sm-3 col-md-3">
				<div class="wow fadeInUp" data-wow-delay="0.2s">
                <div class="service-box">
					<div class="service-icon">
						<img src="<?php echo get_template_directory_uri(); ?>/img/icons/service-icon-2.png" alt="" />
					</div>
					<div class="service-desc">
						<h5>Web Design</h5>
						<p>Vestibulum tincidunt enim in pharetra malesuada. Duis semper magna metus electram accommodare.</p>
					</div>
                </div>
				</div>
            </div>
			<div class="col-sm-3 col-md-3">
				<div class="wow fadeInUp" data-wow-delay="0.2s">
                <div class="service-box">
					<div class="service-icon">
						<img src="<?php echo get_template_directory_uri(); ?>/img/icons/service-icon-3.png" alt="" />
					</div>
					<div class="service-desc">
						<h5>Photography</h5>
						<p>Vestibulum tincidunt enim in pharetra malesuada. Duis semper magna metus electram accommodare.</p>
					</div>
                </div>
				</div>
            </div>
			<div class="col-sm-3 col-md-3">
				<div class="wow fadeInRight" data-wow-delay="0.2s">
                <div class="service-box">
					<div class="service-icon">
						<img src="<?php echo get_template_directory_uri(); ?>/img/icons/service-icon-4.png" alt="" />
					</div>
					<div class="service-desc">
						<h5>Cloud System</h5>
						<p>Vestibulum tincidunt enim in pharetra malesuada. Duis semper magna metus electram accommodare.</p>
					</div>
                </div>
				</div>
            </div>
        </div>		
		</div>
	</section>
	<!-- /Section: services -->
	

	

	<!-- Section: contact -->
    <section id="contact" class="home-section text-center">
		<div class="heading-contact">
			<div class="container">
			<div class="row">
				<div class="col-lg-8 col-lg-offset-2">
					<div class="wow bounceInDown" data-wow-delay="0.4s">
					<div class="section-heading">
					<h2>Get in touch</h2>
					<i class="fa fa-2x fa-angle-down"></i>

					</div>
					</div>
				</div>
			</div>
			</div>
		</div>
		<div class="container">

		<div class="row">
			<div class="col-lg-2 col-lg-offset-5">
				<hr class="marginbot-50">
			</div>
		</div>
    <div class="row">
        <div class="col-lg-8">
            <div class="boxed-grey">
                <form id="contact-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">
                                Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter name" required="required" />
                        </div>
                        <div class="form-group">
                            <label for="email">
                                Email Address</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                                </span>
                                <input type="email" class="form-control" id="email" placeholder="Enter email" required="required" /></div>
                        </div>
                        <div class="form-group">
                            <label for="subject">
                                Subject</label>
                            <select id="subject" name="subject" class="form-control" required="required">
                                <option value="na" selected="">Choose One:</option>
                                <option value="service">General Customer Service</option>
                                <option value="suggestions">Suggestions</option>
                                <option value="product">Product Support</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">
                                Message</label>
                            <textarea name="message" id="message" class="form-control" rows="9" cols="25" required="required"
                                placeholder="Message"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-skin pull-right" id="btnContactUs">
                            Send Message</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
		
		<div class="col-lg-4">
			<div class="widget-contact">
				<h5>Main Office</h5>
				
				<address>
				  <strong>Squas Design, Inc.</strong><br>
				  Tower 795 Folsom Ave, Beautiful Suite 600<br>
				  San Francisco, CA 94107<br>
				  <abbr title="Phone">P:</abbr> (123) 456-7890
				</address>

				<address>
				  <strong>Email</strong><br>
				  <a href="mailto:#">email.name@example.com</a>
				</address>	
				<address>
				  <strong>We're on social networks</strong><br>
                       	<ul class="company-social">
                            <li class="social-facebook"><a href="#" target="_blank"><i class="fa fa-facebook"></i></a></li>
                            <li class="social-twitter"><a href="#" target="_blank"><i class="fa fa-twitter"></i></a></li>
                            <li class="social-dribble"><a href="#" target="_blank"><i class="fa fa-dribbble"></i></a></li>
                            <li class="social-google"><a href="#" target="_blank"><i class="fa fa-google-plus"></i></a></li>
                        </ul>	
				</address>					
			
			</div>	
		</div>
    </div>	

		</div>
	</section>
	<!-- /Section: contact -->

	<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-lg-12">
					<div class="wow shake" data-wow-delay="0.4s">
					<div class="page-scroll marginbot-30">
						<a href="#intro" id="totop" class="btn btn-circle">
							<i class="fa fa-angle-double-up animated"></i>
						</a>
					</div>
					</div>
					<p>&copy;Copyright 2014 - Squad. All rights reserved. Designed by <a href="http://bootstraptaste.com">Bootstrap Themes</a></p>
                    <!-- 
                        All links in the footer should remain intact. 
                        Licenseing information is available at: http://bootstraptaste.com/license/
                        You can buy this theme without footer links online at: http://bootstraptaste.com/buy/?theme=Squadfree
                    -->
				</div>
			</div>	
		</div>
	</footer>


<?php wp_footer(); ?>
</body>

</html>
