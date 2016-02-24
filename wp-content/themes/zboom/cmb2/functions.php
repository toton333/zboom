<?php
 
function zboomMetabox_show_if_front_page( $cmb ) {
	// Don't show this metabox if it's not the front page template
	if ( $cmb->object_id !== get_option( 'page_on_front' ) ) {
		return false;
	}
	return true;
}

/**
 * Conditionally displays a field when used as a callback in the 'show_on_cb' field parameter
 *
 * @param  CMB2_Field object $field Field object
 *
 * @return bool                     True if metabox should show
 */
function zboomMetabox_hide_if_no_cats( $field ) {
	// Don't show this field if not in the cats category
	if ( ! has_tag( 'cats', $field->object_id ) ) {
		return false;
	}
	return true;
}

/**
 * Conditionally displays a message if the $post_id is 2
 *
 * @param  array             $field_args Array of field parameters
 * @param  CMB2_Field object $field      Field object
 */
function zboomMetabox_before_row_if_2( $field_args, $field ) {
	if ( 2 == $field->object_id ) {
		echo '<p>Testing <b>"before_row"</b> parameter (on $post_id 2)</p>';
	} else {
		echo '<p>Testing <b>"before_row"</b> parameter (<b>NOT</b> on $post_id 2)</p>';
	}
}

add_action( 'cmb2_admin_init', 'zboomMetabox_register_demo_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function zboomMetabox_register_demo_metabox() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_zboomMetabox_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'Test Metabox', 'zboom' ),
		'object_types'  => array( 'post', ), // Post type
		// 'show_on_cb' => 'zboomMetabox_show_if_front_page', // function should return a bool value
		 'context'    => 'normal',
		 //'priority'   => 'high',
		// 'show_names' => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // true to keep the metabox closed by default
	) );

	$cmb_demo->add_field( array(
		'name'       => __( 'Test Text', 'zboom' ),
		'desc'       => __( 'field description (optional)', 'zboom' ),
		'id'         => $prefix . 'text',
		'type'       => 'text',
		//'show_on_cb' => 'zboomMetabox_hide_if_no_cats', // function should return a bool value
		// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
		// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
		// 'on_front'        => false, // Optionally designate a field to wp-admin only
		// 'repeatable'      => true,
	) );


	$cmb_demo->add_field( array(
		'name'       => __( 'Multiple choice', 'zboom' ),
		'desc'       => __( 'field description (optional)', 'zboom' ),
		'id'         => $prefix . 'multicheck',
		'type'       => 'multicheck',
		'options'    => array(
             '1' => 'India',
             '2' => 'Australia',
             '3' => 'England'

			)
		//'show_on_cb' => 'zboomMetabox_hide_if_no_cats', // function should return a bool value
		// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
		// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
		// 'on_front'        => false, // Optionally designate a field to wp-admin only
		// 'repeatable'      => true,
	) );

	$cmb_demo->add_field(array(
          'name'      => __( 'Image upload', 'zboom' ),
          'desc'      => __( 'You can upload multiple images', 'zboom' ),
          'id'        => $prefix.'image',
          'type'      => 'file_list',
          'options'   => array(
              'add_upload_files_text' => 'Add image/images',
          	)
		));

	$cmb_demo->add_field( array(
			'name'       => __( 'Multiple choice', 'zboom' ),
			'desc'       => __( 'field description (optional)', 'zboom' ),
			'id'         => $prefix . 'multicheckrepeatable',
			'type'       => 'multicheck',
			'options'    => array(
	             '1' => 'you',
	             '2' => 'me',
	             '3' => 'world'

				),
			//'show_on_cb' => 'zboomMetabox_hide_if_no_cats', // function should return a bool value
			// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
			// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
			// 'on_front'        => false, // Optionally designate a field to wp-admin only
			 'repeatable'      => true,
		) );
	

	
}

//metabox for page

add_action('cmb2_admin_init', 'zboom_metabox_for_page' );
function zboom_metabox_for_page(){

	$prefix = '_zboomPageMetabox_';

	$cmb2_page = new_cmb2_box(array(
          'id' => $prefix.'PageMeta',
          'title' => __( 'Slide Optons', 'zboom' ),
          'object_types' => array('page'),

		));

	$cmb2_page_id = $cmb2_page->add_field(array(
               'id' => $prefix.'slider',
               'description' => __( 'Slider details', 'zboom' ),
               'type' => 'group',
               'options' => array(
                    'group_title' => __( 'Slider {#}', 'zboom' ),
                    'add_button' =>  __( 'Add new slider', 'zboom' ),
                    'remove_button' => __( 'Remove slider', 'zboom' ),
                    'sortable' => true

               	)
 
		));

    $cmb2_page->add_group_field($cmb2_page_id, array(

               'id' => 'title',
               'name' => __( 'Image Title', 'zboom' ),
               'type' => 'text'
    	));

    $cmb2_page->add_group_field($cmb2_page_id, array(

               'id' => 'description',
               'name' => __( 'Description', 'zboom' ),
               'description' => __( 'A short description of this image', 'zboom' ),
               'type' => 'textarea_small',

    	));
    $cmb2_page->add_group_field($cmb2_page_id, array(

               'id' => 'image',
               'name' => __( 'Image', 'zboom' ),
               'type' => 'file'
    	));

    $cmb2_page->add_group_field($cmb2_page_id, array(

               'id' => 'caption',
               'name' => __( 'Image caption', 'zboom' ),
               'type' => 'text'
    	));


 
	
}














