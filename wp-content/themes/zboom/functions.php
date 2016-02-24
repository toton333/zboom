<?php 
if (file_exists(dirname(__file__) .'/cmb2/init.php' )) {
  include_once('cmb2/init.php');
}

if (file_exists(dirname(__file__) .'/cmb2/functions.php' )) {
  include_once('cmb2/functions.php');
  }

if (file_exists(dirname(__file__) .'/redux-framework-master/ReduxCore/framework.php' )) {
  include_once('redux-framework-master/ReduxCore/framework.php');

}
if (file_exists(dirname(__file__) .'/redux-framework-master/sample/config.php'  )) {
  include_once('redux-framework-master/sample/config.php');
}


if ( ! function_exists( 'zboom_setup' ) ) :
function zboom_setup() {

	 
	load_theme_textdomain( 'zboom', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'title-tag' );

	
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 825, 510, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu',      'zboom' ),
	) );

	
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );



	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background');

 //to use shortcode on widgets
  add_filter('widget_text', 'do_shortcode' );

}
endif; // zboom_setup
add_action( 'after_setup_theme', 'zboom_setup' );

function zboom_enqueue_scripts(){

//register stylesheets
wp_register_style( 'main_style', get_stylesheet_uri());
wp_register_style( 'zerogrid', get_template_directory_uri().'/css/zerogrid.css');
wp_register_style( 'responsive', get_template_directory_uri().'/css/responsive.css');
wp_register_style( 'responsiveslides', get_template_directory_uri().'/css/responsiveslides.css');


//enqueue stylesheets
wp_enqueue_style( 'main_style');
wp_enqueue_style( 'zerogrid');
wp_enqueue_style( 'responsive');
wp_enqueue_style( 'responsiveslides');


//register scripts
wp_register_script( 'responsiveslides', get_template_directory_uri().'/js/responsiveslides.js', array('jquery'), '1.0', true );
wp_register_script( 'init', get_template_directory_uri().'/js/init.js', array('jquery', 'responsiveslides'), '1.0', true );


//enqueue scripts
wp_enqueue_script( 'responsiveslides');
wp_enqueue_script('init' );

}
add_action('wp_enqueue_scripts', 'zboom_enqueue_scripts');

//zboom admin enqueue scripts

function zboom_admin_enqueue_scripts(){

wp_register_style( 'font-awesome', get_template_directory_uri().'/css/font-awesome.min.css');
wp_enqueue_style('font-awesome' );

}

add_action( 'admin_enqueue_scripts', 'zboom_admin_enqueue_scripts');
add_action('wp_enqueue_scripts', 'zboom_admin_enqueue_scripts' );




//read more function

function read_more($limit = 30){

	$content = explode(" ", get_the_content());
	$less_content = array_slice($content, 0, $limit);
	echo implode(" ", $less_content);
}

//custom post types register

function zboom_custom_posts(){
 register_post_type( 'slider', array(
   'label' => __('Sliders','zboom'),
   'labels' => array(
         'name' => __('Sliders', 'zboom'),
         'all_items'=> __('All Sliders', 'zboom'),
         'add_new_item' => __('Add new Slider', 'zboom')
   	),
   'description' => __('This post type is used to add banner sliders.', 'zboom'),
   'supports' =>array('thumbnail', 'title'),
   'public' => true,
   'menu_icon' => get_template_directory_uri().'/images/iconfinder_slide.png'



 	) );


 register_post_type( 'block', array(

       'label' => __( 'Blocks', 'zboom' ),
       'labels' => array(
             'name' => __( 'Blocks', 'zboom' ),
             'add_new_item' => __( 'Add New Block', 'zboom' ),
             'all_items' => __( 'All Blocks', 'zboom' ),
       	),
       'public' => true,
       'description' => __( 'Post types to fill up block areas', 'zboom' ),
       'supports' => array('title', 'editor', 'thumbnail'),
       'menu_icon' => 'dashicons-editor-table'


 	) );

   register_post_type('gallery', array(

      'labels' => array(
           'name'         => __( 'Gallery Items', 'zboom' ),
           'add_new_item' => __( 'Add New Gallery Item', 'zboom' ),
           'all_items'    => __( 'All Gallery Items', 'zboom' )
        ),

      'public' => true,
      'description' => __( 'Post type to create a gallery', 'zboom' ),
      'supports' => array('title', 'editor', 'thumbnail'),
      'menu_icon' => 'dashicons-format-image',

    ));

 
}
add_action('init', 'zboom_custom_posts');

// widget register

function zboom_widget(){
    
	register_sidebar(array(
       'id' => 'right_sidebar',
       'name' => __('Right Sidebar', 'zboom'),
       'description' => __('Container for right side\'s widgets', 'zboom'),
       'before_widget' => '<div class="box right_sidebar">',
       'after_widget' => '</div></div>',
       'before_title' => '<div class="heading"><h2>',
       'after_title'  => '</h2></div><div class="content">'
    	) );
	register_sidebar(array(
       'id' => 'contact_sidebar',
       'name' => __('Contact Sidebar', 'zboom'),
       'description' => __('Container for contact side\'s widgets', 'zboom'),
       'before_widget' => '<div class="box">',
       'after_widget' => '</div></div>',
       'before_title' => '<div class="heading"><h2>',
       'after_title'  => '</h2></div><div class="content">'
    	) );

	register_sidebar(array(
        'id' => 'footer_sidebar',
        'name' => __('Footer Sidebar', 'zboom'),
        'description' => __('Container for footer widgets', 'zboom'),
        'before_widget' => '<div class="col-1-4">
				<div class="wrap-col">
					<div class="box">',
        'after_widget' => '</div>
					</div>
				</div>
			</div>',
        'before_title' => '<div class="heading"><h2>',
        'after_title' => '</h2></div>
						<div class="content">'

		) );

}
add_action('widgets_init', 'zboom_widget');

/*
//create user without dashboard acccess

$newuser = new WP_User(wp_create_user('username', 'password', 'bishan12333@yahoo.com'));
$newuser->set_role('administrator');
*/

//shortcode

function zboom_shortcode($atts, $content){

$heading_atts = extract(shortcode_atts(array(

   'position' => 'right',


  ), $atts ));

 return '<h1 align="'.$position.'">'.$content.'</h1>';
 
}
add_shortcode('heading', 'zboom_shortcode');

//meta box

function zboom_favfood_metabox(){

  add_meta_box( 'favfood', 'Favorite Foods', 'zboom_favfood_display', 'page', 'side', 'high');

}
add_action('add_meta_boxes', 'zboom_favfood_metabox' );


function zboom_favfood_display($post){

  // Add a nonce field so we can check for it later.
    wp_nonce_field( 'zboom_save_meta_box_data', 'zboom_meta_box_nonce' );

$zboom_meta = get_post_meta( $post->ID, 'zboom_meta', true );
echo $food = $zboom_meta['food'];
echo $price = $zboom_meta['price'];

?>
<p>
 <label for="indian">Favorite Indian Food:</label>
 <input type="text" id="indian" name="indian[food]"  value=" <?php echo esc_attr( $food); ?> " >
</p>
<p>
 <label for="price">Price:</label>
 <select name="indian[price]" id="price">
   <option value="1" <?php selected($price, 1 ); ?>  >Below $3</option>
   <option value="2" <?php selected($price, 2 ); ?>  >$3-$10</option>
   <option value="3" <?php selected($price, 3 ); ?>  >$10</option>
 </select>
</p>


<?php
}

function zboom_favfood_save($post_id){

  /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['zboom_meta_box_nonce'] ) ) {
      return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['zboom_meta_box_nonce'], 'zboom_save_meta_box_data' ) ) {
      return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

      if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
      }

    } else {

      if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
      }
    }


  // Make sure that it is set.
    if (  isset( $_POST['indian']['food'] ) && isset($_POST['indian']['price']) ) {
         
            $new_indian = array();
            foreach ($_POST['indian'] as $key => $value) {
              $new_indian["$key"] = sanitize_text_field($value ); 
            }

              update_post_meta( $post_id, 'zboom_meta', $new_indian );

    }else{
      return;
    }


}
add_action('save_post', 'zboom_favfood_save' );


/**
 * Sample template tag function for outputting a cmb2 file_list
 *
 * @param  string  $file_list_meta_key The field meta key. ('wiki_test_file_list')
 * @param  string  $img_size           Size of image to show
 */
function cmb2_output_file_list( $file_list_meta_key, $img_size = 'medium' ) {

    // Get the list of files
    $files = get_post_meta( get_the_ID(), $file_list_meta_key, 1 );

    echo '<div class="file-list-wrap">';
    // Loop through them and output an image
    foreach ( (array) $files as $attachment_id => $attachment_url ) {
        echo '<div class="file-list-image">';
        echo wp_get_attachment_image( $attachment_id, $img_size );
        echo '</div>';
    }
    echo '</div>';
}








