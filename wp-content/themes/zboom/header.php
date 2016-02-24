<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php language_attributes() ?> > <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes() ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes() ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes() ?>> <!--<![endif]-->
<head>

    <!-- Basic Page Needs
  ================================================== -->
	<meta charset="<?php bloginfo('charset' ); ?>">
	<meta name="description" content="Free Html5 Templates and Free Responsive Themes Designed by Kimmy | zerotheme.com">
	<meta name="author" content="www.zerotheme.com">
	
    <!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<style type="text/css">
	<?php global $redux_demo; ?>
    body{
    	background-color: <?php echo $redux_demo['opt-background']['background-color']; ?> !important ;
    	background-image: url("<?php echo $redux_demo['opt-background']['background-image']; ?>") !important;
    	background-repeat: <?php echo $redux_demo['opt-background']['background-repeat']; ?> !important ;
    	background-size: <?php echo $redux_demo['opt-background']['background-size']; ?> !important ;
    	background-attachment: <?php echo $redux_demo['opt-background']['background-attachment']; ?> !important ;

    }
	nav .menu  ul li a {color: <?php echo $redux_demo['nav-color']; ?> !important;}
	footer .wrap-footer{color: <?php echo $redux_demo['footer-color']; ?> !important ;}

	nav .wrap-nav{

		border-style: <?php echo $redux_demo['opt-border']['border-style']; ?> ;
		border-color: <?php echo $redux_demo['opt-border']['border-color']; ?> ;
		border-width: <?php echo $redux_demo['opt-border']['border-top']; ?> ;
	}




     <?php echo $redux_demo['opt-css']; ?>
	</style>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header>
	<div class="wrap-header zerogrid">
		<?php
		 global $redux_demo; 
		 $logo_url = $redux_demo['opt-media']['url'];
		 ?>
		<div id="logo"><a href="<?php echo home_url(); ?>"><img src= "<?php echo $logo_url; ?>"></a></div>
		
		<div id="search">
			<div class="button-search"></div>
			<form action="<?php echo esc_url(home_url( '/') ); ?>" method="GET"  >
			<input name="s" id ="s" type="text" placeholder="<?php echo esc_attr_x( 'Search …', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>">
			</form>
		</div>
	</div>
</header>

<nav>
	<div class="wrap-nav zerogrid">

		<?php
		$args = array(
			'theme_location' => 'primary',
            'container_class' => 'menu'
			); 

		wp_nav_menu( $args );

		 ?>
		
		<div class="minimenu"><div>MENU</div>
			<select onchange="location=this.value">
				<option></option>
				<option value="index.html">Home</option>
				<option value="blog.html">Blog</option>
				<option value="gallery.html">Gallery</option>
				<option value="single.html">About</option>
				<option value="contact.html">Contact</option>
			</select>
		</div>		
	</div>
</nav>
