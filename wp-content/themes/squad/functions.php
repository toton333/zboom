<?php


if ( ! function_exists( 'squad_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Twenty Fifteen 1.0
 */
function squad_setup() {

	load_theme_textdomain( 'squad', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 825, 510, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu',      'squad' )
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );


	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
}
endif; // squad_setup
add_action( 'after_setup_theme', 'squad_setup' );


function squad_scripts(){
//registering styles
wp_register_style( 'bootstrap', get_template_directory_uri().'/css/bootstrap.min.css' );
wp_register_style( 'font-awesome', get_template_directory_uri().'/font-awesome/css/font-awesome.min.css' );
wp_register_style( 'animate', get_template_directory_uri().'/css/animate.css' );
wp_register_style( 'style', get_template_directory_uri().'/css/style.css' );
wp_register_style( 'default', get_template_directory_uri().'/color/default.css' );


//enqueueing styles
wp_enqueue_style('bootstrap' );
wp_enqueue_style('font-awesome' );
wp_enqueue_style('animate' );
wp_enqueue_style('style' );
wp_enqueue_style('default' );

//stylesheet
wp_enqueue_style( 'main-style', get_stylesheet_uri());

//registering scripts
wp_register_script( 'bootstrap', get_template_directory_uri().'/js/bootstrap.min.js', array('jquery'), '1.0', true);
wp_register_script( 'easing', get_template_directory_uri().'/js/jquery.easing.min.js', array('jquery'), '1.0', true);
wp_register_script( 'scrollTo', get_template_directory_uri().'/js/jquery.scrollTo.js', array('jquery'), '1.0', true);
wp_register_script( 'wow', get_template_directory_uri().'/js/wow.min.js', array('jquery'), '1.0', true);
wp_register_script( 'custom', get_template_directory_uri().'/js/custom.js', array('jquery'), '1.0', true);

wp_enqueue_script( 'bootstrap');
wp_enqueue_script( 'easing');
wp_enqueue_script( 'scrollTo');
wp_enqueue_script( 'wow');
wp_enqueue_script('custom' );


}

add_action('wp_enqueue_scripts', 'squad_scripts' );


	require_once('wp_bootstrap_navwalker.php');















