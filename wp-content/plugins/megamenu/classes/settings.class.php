<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu_Settings' ) ) :

/**
 * Handles all admin related functionality.
 */
class Mega_Menu_Settings{


    /**
     * All themes (default and custom)
     */
    var $themes = array();


    /**
     * Active theme
     */
    var $active_theme = array();


    /**
     * Active theme ID
     */
    var $id = "";


    /**
     * Constructor
     *
     * @since 1.0
     */
    public function __construct() {

        add_action( 'admin_post_megamenu_save_theme', array( $this, 'save_theme') );
        add_action( 'admin_post_megamenu_add_theme', array( $this, 'create_theme') );
        add_action( 'admin_post_megamenu_delete_theme', array( $this, 'delete_theme') );
        add_action( 'admin_post_megamenu_revert_theme', array( $this, 'revert_theme') );
        add_action( 'admin_post_megamenu_import_theme', array( $this, 'import_theme') );
        add_action( 'admin_post_megamenu_duplicate_theme', array( $this, 'duplicate_theme') );

        add_action( 'admin_post_megamenu_add_menu_location', array( $this, 'add_menu_location') );
        add_action( 'admin_post_megamenu_delete_menu_location', array( $this, 'delete_menu_location') );

        add_action( 'admin_post_megamenu_save_settings', array( $this, 'save_settings') );
        add_action( 'admin_post_megamenu_clear_css_cache', array( $this, 'tools_clear_css_cache') );
        add_action( 'admin_post_megamenu_delete_data', array( $this, 'delete_data') );

        add_action( 'megamenu_page_theme_editor', array( $this, 'theme_editor_page'));
        add_action( 'megamenu_page_tools', array( $this, 'tools_page'));
        add_action( 'megamenu_page_general_settings', array( $this, 'general_settings_page'));

        add_action( 'admin_menu', array( $this, 'megamenu_themes_page') );
        add_action( 'megamenu_admin_scripts', array( $this, 'enqueue_scripts' ) );

    }

    /**
     *
     * @since 1.4
     */
    public function init() {

        if ( class_exists( "Mega_Menu_Style_Manager" ) ) {

            $style_manager = new Mega_Menu_Style_Manager();

            $this->themes = $style_manager->get_themes();
            $this->id = isset( $_GET['theme'] ) ? $_GET['theme'] : 'default';
            $this->active_theme = $this->themes[$this->id];

        }

    }

    /**
     * Save changes to an exiting theme.
     *
     * @since 1.0
     */
    public function save_theme() {

        check_admin_referer( 'megamenu_save_theme' );

        $theme = esc_attr( $_POST['theme_id'] );

        $saved_themes = get_site_option( "megamenu_themes" );

        if ( isset( $saved_themes[ $theme ] ) ) {
            unset( $saved_themes[ $theme ] );
        }

        $submitted_settings = $_POST['settings'];

        if ( isset( $_POST['checkboxes'] ) ) {

            foreach ( $_POST['checkboxes'] as $checkbox => $value ) {

                if ( isset( $submitted_settings[ $checkbox ] ) ) {

                    $submitted_settings[ $checkbox ] = 'on';

                } else {

                    $submitted_settings[ $checkbox ] = 'off';

                }

            }

        }

        $saved_themes[ $theme ] = array_map( 'esc_attr', $submitted_settings );

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_save");

        do_action("megamenu_delete_cache");

        $this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$theme}&saved=true" ) );

    }


    /**
     * Add a new menu location.
     *
     * @since 1.8
     */
    public function add_menu_location() {

        check_admin_referer( 'megamenu_add_menu_location' );

        $locations = get_option( 'megamenu_locations' );

        $next_id = $this->get_next_menu_location_id();

        $new_menu_location_id = "max_mega_menu_" . $next_id;

        $locations[$new_menu_location_id] = "Max Mega Menu Location " . $next_id;

        update_option( 'megamenu_locations', $locations );

        do_action("megamenu_after_add_menu_location");

        $this->redirect( admin_url( 'admin.php?page=maxmegamenu&add_location=true' ) );

    }


    /**
     * Delete a menu location.
     *
     * @since 1.8
     */
    public function delete_menu_location() {

        check_admin_referer( 'megamenu_delete_menu_location' );

        $locations = get_option( 'megamenu_locations' );

        $location_to_delete = esc_attr( $_GET['location'] );

        if ( isset( $locations[ $location_to_delete ] ) ) {
            unset( $locations[ $location_to_delete ] );
            update_option( 'megamenu_locations', $locations );
        }

        do_action("megamenu_after_delete_menu_location");

        do_action("megamenu_delete_cache");

        $this->redirect( admin_url( 'admin.php?page=maxmegamenu&delete_location=true' ) );

    }

    /**
     * Clear the CSS cache.
     *
     * @since 1.5
     */
    public function tools_clear_css_cache() {

        check_admin_referer( 'megamenu_clear_css_cache' );

        do_action( 'megamenu_delete_cache' );

        $this->redirect( admin_url( 'admin.php?page=maxmegamenu_tools&clear_css_cache=true' ) );

    }


    /**
     * Deletes all Max Mega Menu data from the database
     *
     * @since 1.5
     */
    public function delete_data() {

        check_admin_referer( 'megamenu_delete_data' );

        do_action("megamenu_delete_cache");

        // delete menu settings
        delete_option("megamenu_settings");

        // delete menu locations
        delete_option("megamenu_locations");

        // delete all widgets assigned to menus
        $widget_manager = new Mega_Menu_Widget_Manager();

        if ( $mega_menu_widgets = $widget_manager->get_mega_menu_sidebar_widgets() ) {

            foreach ( $mega_menu_widgets as $widget_id ) {

                $widget_manager->delete_widget( $widget_id );

            }

        }

        // delete all mega menu metadata stored against menu items
        delete_metadata( 'post', 0, '_megamenu', '', true );

        // clear cache
        delete_transient( "megamenu_css" );

        // delete custom themes
        delete_site_option( "megamenu_themes" );

        $this->redirect( admin_url( "admin.php?page=maxmegamenu_tools&delete_data=true" ) );

    }

    /**
     * Save menu general settings.
     *
     * @since 1.0
     */
    public function save_settings() {

        check_admin_referer( 'megamenu_save_settings' );

        $submitted_settings = apply_filters( "megamenu_submitted_settings", $_POST['settings'] );

        $existing_settings = get_option( 'megamenu_settings' );

        $new_settings = array_merge( (array)$existing_settings, $submitted_settings );

        update_option( 'megamenu_settings', $new_settings );

        // update location description
        if ( isset( $_POST['location'] ) && is_array( $_POST['location'] ) ) {

            $locations = get_option('megamenu_locations');

            $new_locations = array_merge( (array)$locations, $_POST['location'] );

            update_option( 'megamenu_locations', $new_locations );
        }

        do_action("megamenu_after_save_general_settings");

        do_action("megamenu_delete_cache");

        $url = isset( $_POST['_wp_http_referer'] ) ? $_POST['_wp_http_referer'] : admin_url( "admin.php?page=maxmegamenu&saved=true" );

        $this->redirect( $url );

    }


    /**
     * Duplicate an existing theme.
     *
     * @since 1.8
     */
    public function import_theme() {

        check_admin_referer( 'megamenu_import_theme' );

        $this->init();

        $import = json_decode( stripslashes( $_POST['data'] ), true );

        if ( is_array( $import ) ) {

            $saved_themes = get_site_option( "megamenu_themes" );

            $next_id = $this->get_next_theme_id();

            $import['title'] = $import['title'] . " " . __('(Imported)', 'megamenu');

            $new_theme_id = "custom_theme_" . $next_id;

            $saved_themes[ $new_theme_id ] = $import;

            update_site_option( "megamenu_themes", $saved_themes );

            do_action("megamenu_after_theme_import");

            $this->redirect( admin_url( "admin.php?page=maxmegamenu_tools&theme_imported=true") );

        } else {

            $this->redirect( admin_url( "admin.php?page=maxmegamenu_tools&theme_imported=false") );

        }


    }

    /**
     * Duplicate an existing theme.
     *
     * @since 1.0
     */
    public function duplicate_theme() {

        check_admin_referer( 'megamenu_duplicate_theme' );

        $this->init();

        $theme = esc_attr( $_GET['theme_id'] );

        $copy = $this->themes[$theme];

        $saved_themes = get_site_option( "megamenu_themes" );

        $next_id = $this->get_next_theme_id();

        $copy['title'] = $copy['title'] . " " . __('Copy', 'megamenu');

        $new_theme_id = "custom_theme_" . $next_id;

        $saved_themes[ $new_theme_id ] = $copy;

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_duplicate");

        $this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$new_theme_id}&duplicated=true") );

    }


    /**
     * Delete a theme
     *
     * @since 1.0
     */
    public function delete_theme() {

        check_admin_referer( 'megamenu_delete_theme' );

        $theme = esc_attr( $_GET['theme_id'] );

        if ( $this->theme_is_being_used_by_location( $theme ) ) {

            $this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$theme}&deleted=false") );
            return;
        }

        $saved_themes = get_site_option( "megamenu_themes" );

        if ( isset( $saved_themes[$theme] ) ) {
            unset( $saved_themes[$theme] );
        }

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_delete");

        do_action("megamenu_delete_cache");

        $this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme=default&deleted=true") );

    }


    /**
     * Revert a theme (only available for default themes, you can't revert a custom theme)
     *
     * @since 1.0
     */
    public function revert_theme() {

        check_admin_referer( 'megamenu_revert_theme' );

        $theme = esc_attr( $_GET['theme_id'] );

        $saved_themes = get_site_option( "megamenu_themes" );

        if ( isset( $saved_themes[$theme] ) ) {
            unset( $saved_themes[$theme] );
        }

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_revert");

        do_action("megamenu_delete_cache");

        $this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$theme}&reverted=true") );

    }


    /**
     * Create a new custom theme
     *
     * @since 1.0
     */
    public function create_theme() {

        check_admin_referer( 'megamenu_create_theme' );

        $this->init();

        $saved_themes = get_site_option( "megamenu_themes" );

        $next_id = $this->get_next_theme_id();

        $new_theme_id = "custom_theme_" . $next_id;

        $new_theme = $this->themes['default'];

        $new_theme['title'] = "Custom {$next_id}";

        $saved_themes[$new_theme_id] = $new_theme;

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_create");

        $this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$new_theme_id}&created=true") );

    }


    /**
     * Redirect and exit
     *
     * @since 1.8
     */
    public function redirect( $url ) {

        wp_redirect( $url );
        exit;

    }


    /**
     * Returns the next available menu location ID
     *
     * @since 1.0
     */
    public function get_next_menu_location_id() {

        $last_id = 0;

        if ( $locations = get_option( "megamenu_locations" ) ) {

            foreach ( $locations as $key => $value ) {

                if ( strpos( $key, 'max_mega_menu_' ) !== FALSE ) {

                    $parts = explode( "_", $key );
                    $menu_id = end( $parts );

                    if ($menu_id > $last_id) {
                        $last_id = $menu_id;
                    }

                }

            }

        }

        $next_id = $last_id + 1;

        return $next_id;
    }

    /**
     * Returns the next available custom theme ID
     *
     * @since 1.0
     */
    public function get_next_theme_id() {

        $last_id = 0;

        if ( $saved_themes = get_site_option( "megamenu_themes" ) ) {

            foreach ( $saved_themes as $key => $value ) {

                if ( strpos( $key, 'custom_theme' ) !== FALSE ) {

                    $parts = explode( "_", $key );
                    $theme_id = end( $parts );

                    if ($theme_id > $last_id) {
                        $last_id = $theme_id;
                    }

                }

            }

        }

        $next_id = $last_id + 1;

        return $next_id;
    }


    /**
     * Checks to see if a certain theme is in use.
     *
     * @since 1.0
     * @param string $theme
     */
    public function theme_is_being_used_by_location( $theme ) {
        $settings = get_option( "megamenu_settings" );

        if ( ! $settings ) {
            return false;
        }

        $locations = get_nav_menu_locations();

        $menus = get_registered_nav_menus();


        $theme_in_use_locations = array();

        if ( count( $locations ) ) {

            foreach ( $locations as $location => $menu_id ) {

                if ( has_nav_menu( $location ) && isset( $settings[ $location ]['theme'] ) && $settings[ $location ]['theme'] == $theme ) {
                    $theme_in_use_locations[] = isset( $menus[ $location ] ) ? $menus[ $location ] : $location;
                }

            }

            if ( count( $theme_in_use_locations ) ) {
                return $theme_in_use_locations;
            }

        }

        return false;
    }


    /**
     * Adds the "Menu Themes" menu item and page.
     *
     * @since 1.0
     */
    public function megamenu_themes_page() {

        $page = add_menu_page( __('Max Mega Menu', 'megamenu'), __('Mega Menu', 'megamenu'), 'edit_theme_options', 'maxmegamenu', array($this, 'page') );


        $tabs = apply_filters("megamenu_menu_tabs", array(
            'general_settings' => __("General Settings", "megamenu"),
            'theme_editor' => __("Menu Themes", "megamenu"),
            'tools' => __("Tools", "megamenu")
        ));

        foreach ( $tabs as $key => $title ) {
            if ( $key == 'general_settings') {
                add_submenu_page( 'maxmegamenu', __('Max Mega Menu', 'megamenu') . ' - ' . $title, $title, 'edit_theme_options', 'maxmegamenu', array($this, 'page') );
            } else {
                add_submenu_page( 'maxmegamenu', __('Max Mega Menu', 'megamenu') . ' - ' . $title, $title, 'edit_theme_options', 'maxmegamenu_' . $key, array($this, 'page') );
            }
        }

    }


    /**
     * Content for 'Settings' tab
     *
     * @since 1.4
     */
    public function general_settings_page( $saved_settings ) {

        $all_locations = get_registered_nav_menus();
        $locations = array();

        if ( count( $all_locations ) ) {

            $megamenu_locations = array();

            // reorder locations so custom MMM locations are listed at the bottom
            foreach ( $all_locations as $location => $val ) {

                if ( strpos( $location, 'max_mega_menu_' ) === FALSE ) {
                    $locations[$location] = $val;
                } else {
                    $megamenu_locations[$location] = $val;
                }

            }

            $locations = array_merge( $locations, $megamenu_locations );
        }

        $css = isset( $saved_settings['css'] ) ? $saved_settings['css'] : 'fs';
        $mobile_second_click = isset( $saved_settings['second_click'] ) ? $saved_settings['second_click'] : 'close';
        $mobile_behaviour = isset( $saved_settings['mobile_behaviour'] ) ? $saved_settings['mobile_behaviour'] : 'standard';


        ?>

        <div class='menu_settings'>

            <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                <input type="hidden" name="action" value="megamenu_save_settings" />
                <?php wp_nonce_field( 'megamenu_save_settings' ); ?>

                <h3 class='first'><?php _e("General Settings", "megamenu"); ?></h3>

                <table>
                    <tr>
                        <td class='mega-name'>
                            <?php _e("Click Event Behaviour", "megamenu"); ?>
                            <div class='mega-description'>
                                <?php _e("Define what should happen when the event is set to 'click'. This also applies to mobiles.", "megamenu"); ?>
                            </div>
                        </td>
                        <td class='mega-value'>
                            <select name='settings[second_click]'>
                                <option value='close' <?php echo selected( $mobile_second_click == 'close'); ?>><?php _e("First click will open a sub menu, second click will close the sub menu.", "megamenu"); ?></option>
                                <option value='go' <?php echo selected( $mobile_second_click == 'go'); ?>><?php _e("First click will open a sub menu, second click will follow the link.", "megamenu"); ?></option>
                            <select>
                            <div class='mega-description'>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class='mega-name'>
                            <?php _e("Mobile Menu Behaviour", "megamenu"); ?>
                            <div class='mega-description'>
                                <?php _e("Define the sub menu toggle behaviour for the mobile menu.", "megamenu"); ?>
                            </div>
                        </td>
                        <td class='mega-value'>
                            <select name='settings[mobile_behaviour]'>
                                <option value='standard' <?php echo selected( $mobile_behaviour == 'standard'); ?>><?php _e("Standard - Open sub menus will remain open until closed by the user.", "megamenu"); ?></option>
                                <option value='accordion' <?php echo selected( $mobile_behaviour == 'accordion'); ?>><?php _e("Accordion - Open sub menus will automatically close when another one is opened.", "megamenu"); ?></option>
                            <select>
                            <div class='mega-description'>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class='mega-name'>
                            <?php _e("CSS Output", "megamenu"); ?>
                            <div class='mega-description'>
                            </div>
                        </td>
                        <td class='mega-value'>
                            <select name='settings[css]' id='mega_css'>
                                <option value='fs' <?php echo selected( $css == 'fs'); ?>><?php _e("Save to filesystem", "megamenu"); ?></option>
                                <option value='head' <?php echo selected( $css == 'head'); ?>><?php _e("Output in &lt;head&gt;", "megamenu"); ?></option>
                                <option value='ajax' <?php echo selected( $css == 'ajax'); ?>><?php _e("Enqueue dynamically via admin-ajax.php", "megamenu"); ?></option>
                                <option value='disabled' <?php echo selected( $css == 'disabled'); ?>><?php _e("Don't output CSS", "megamenu"); ?></option>
                            <select>
                            <div class='mega-description'>
                                <div class='ajax' style='display: <?php echo $css == 'ajax' ? 'block' : 'none' ?>'><?php _e("CSS will be enqueued dynamically through admin-ajax.php and loaded from the cache.", "megamenu"); ?></div>
                                <div class='fs' style='display: <?php echo $css == 'fs' ? 'block' : 'none' ?>'><?php _e("CSS will be saved to wp-content/uploads/maxmegamenu/style.css and enqueued from there.", "megamenu"); ?></div>
                                <div class='head' style='display: <?php echo $css == 'head' ? 'block' : 'none' ?>'><?php _e("CSS will be loaded from the cache in a &lt;style&gt; tag in the &lt;head&gt; of the page.", "megamenu"); ?></div>
                                <div class='disabled' style='display: <?php echo $css == 'disabled' ? 'block' : 'none' ?>'><?php _e("CSS will not be output, you must enqueue the CSS for the menu manually.", "megamenu"); ?></div>
                            </div>
                        </td>
                    </tr>
                </table>

                <h3 class='first'><?php _e("Menu Locations", "megamenu"); ?></h3>

                <table>
                    <tr>
                        <td class='mega-name'>
                            <?php _e("Registered Menu Locations", "megamenu"); ?>
                            <div class='mega-description'><?php _e("An overview of the menu locations supported by your theme. You can enable Max Mega Menu and adjust the settings for a specific menu location by going to Appearance > Menus."); ?></div>
                        </td>
                        <td class='mega-value'>
                            <p>
                                <?php
                                    $none = __("Your theme does not natively support menus, but you can add a new menu location using Max Mega Menu and display the menu using the Max Mega Menu widget or shortcode.", "megamenu");
                                    $one = __("Your theme supports one menu.", "megamenu");
                                    $some = __("Your theme supports {number} menus.", "megamenu");

                                    if ( ! count($locations)) {
                                        echo $none;
                                    } else if ( count($locations) == 1) {
                                        echo $one;
                                    } else {
                                        echo str_replace( "{number}", count( $locations ), $some );
                                    }

                                    $add_location_url = esc_url( add_query_arg(
                                        array(
                                            'action'=>'megamenu_add_menu_location'
                                        ),
                                        wp_nonce_url( admin_url("admin-post.php"), 'megamenu_add_menu_location' )
                                    ) );
                                    echo " <a href='{$add_location_url}'>" . __("Add another menu location.") . "</a>";

                                ?>
                            </p>

                            <?php

                            if ( count ( $locations ) ) {

                                echo "<div class='accordion-container'>";
                                echo "<ul class='outer-border'>";

                                foreach ( $locations as $location => $description ) {

                                    $menu_id = $this->get_menu_id_for_location( $location );

                                    $is_custom_location = strpos( $location, 'max_mega_menu_' ) !== FALSE;

                                    ?>


                                    <li class='control-section accordion-section mega-location'>
                                        <h4 class='accordion-section-title hndle'>

                                            <?php echo $description ?>

                                            <?php

                                                    echo "<div class='mega-assigned-menu'>";
                                                        echo __("Menu", "megamenu") . ": ";

                                                        if ($menu_id) {
                                                            echo "<a href='" . admin_url("nav-menus.php?action=edit&menu={$menu_id}") . "'>" . $this->get_menu_name_for_location( $location ) . "</a>";
                                                        } else {
                                                            echo "<a href='" . admin_url('nav-menus.php?action=locations') . "'>" . __("Assign a menu", "megamenu") . "</a>";
                                                        }

                                                    echo "</div>";
                                            ?>

                                        </h4>
                                        <div class='accordion-section-content'>

                                            <table>
                                                <?php if ( $is_custom_location ) : ?>
                                                    <tr>
                                                        <td class='mega-name'>
                                                            <?php _e("Location Description", "megamenu"); ?>
                                                            <div class='mega-description'><?php _e("Change the name of the location.", "megamenu"); ?></div>
                                                        </td>
                                                        <td class='mega-value wide'>
                                                            <input type='text' name='location[<?php echo $location ?>]' value='<?php echo $description; ?>' />
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </table>

                                            <h5><?php _e("Menu Display Options", "megamenu"); ?></h5>

                                            <?php if ( ! $is_custom_location ) : ?>
                                            <p><?php _e("These options are for advanced users only. Your theme should already include the code required to display this menu on your site."); ?>
                                            <?php endif; ?>

                                            <table>
                                                <tr>
                                                    <td class='mega-name'>
                                                        <?php _e("PHP Function", "megamenu"); ?>
                                                        <div class='mega-description'><?php _e("For use in a theme template (usually header.php)", "megamenu"); ?></div>
                                                    </td>
                                                    <td class='mega-value'>
                                                        <textarea>&lt;?php wp_nav_menu( array( 'theme_location' => '<?php echo $location ?>' ) ); ?&gt;</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='mega-name'>
                                                        <?php _e("Shortcode", "megamenu"); ?>
                                                        <div class='mega-description'><?php _e("For use in a post or page.", "megamenu"); ?></div>
                                                    </td>
                                                    <td class='mega-value'>
                                                        <textarea>[maxmegamenu location=<?php echo $location ?>]</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='mega-name'>
                                                        <?php _e("Widget", "megamenu"); ?>
                                                        <div class='mega-description'><?php _e("For use in a widget area.", "megamenu"); ?></div>
                                                    </td>
                                                    <td class='mega-value'>
                                                        <?php _e("Add the 'Max Mega Menu' widget to a widget area.", "megamenu") ?>
                                                    </td>
                                                </tr>
                                            </table>


                                            <?php

                                                if ( $is_custom_location ) {

                                                    $delete_location_url = esc_url( add_query_arg(
                                                        array(
                                                            'action' => 'megamenu_delete_menu_location',
                                                            'location' => $location
                                                        ),
                                                        wp_nonce_url( admin_url("admin-post.php"), 'megamenu_delete_menu_location' )
                                                    ) );

                                                    echo "<a class='confirm mega-delete button button-secondary' href='{$delete_location_url}'>" . __("Delete location") . "</a>";

                                                }

                                            ?>

                                        </div>
                                    </li>
                                <?php
                                }

                                echo "</div>";
                                echo "</div>";

                            }?>

                        </td>
                    </tr>
                </table>



                <?php do_action( "megamenu_general_settings", $saved_settings ); ?>

                <?php

                    submit_button();

                ?>
            </form>
        </div>

        <?php
    }


    /**
     * Returns the menu ID for a specified menu location, defaults to 0
     *
     * @since 1.8
     * @param string $location
     */
    private function get_menu_id_for_location( $location ) {

        $locations = get_nav_menu_locations();

        $id = isset( $locations[ $location ] ) ? $locations[ $location ] : 0;

        return $id;

    }

    /**
     * Returns the menu name for a specified menu location
     *
     * @since 1.8
     * @param string $location
     */
    private function get_menu_name_for_location( $location ) {

        $id = $this->get_menu_id_for_location( $location );

        $menus = wp_get_nav_menus();

        foreach ( $menus as $menu ) {
            if ( $menu->term_id == $id ) {
                return $menu->name;
            }
        }

        return false;
    }


    /**
     * Content for 'Tools' tab
     *
     * @since 1.4
     */
    public function tools_page( $saved_settings ) {

        ?>

        <div class='menu_settings'>

            <h3 class='first'><?php _e("Tools", "megamenu"); ?></h3>

            <table>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Cache", "megamenu"); ?>
                        <div class='mega-description'><?php _e("Use this tool to clear the CSS cache.", "megamenu"); ?></div>
                    </td>
                    <td class='mega-value'>
                        <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                            <?php wp_nonce_field( 'megamenu_clear_css_cache' ); ?>
                            <input type="hidden" name="action" value="megamenu_clear_css_cache" />

                            <input type='submit' class='button button-secondary' value='<?php _e("Clear CSS Cache", "megamenu"); ?>' />
                        </form>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Plugin Data", "megamenu"); ?>
                        <div class='mega-description'><?php _e("Delete all saved Max Mega Menu plugin data from the database. Use with caution!", "megamenu"); ?></div>
                    </td>
                    <td class='mega-value'>
                        <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                            <?php wp_nonce_field( 'megamenu_delete_data' ); ?>
                            <input type="hidden" name="action" value="megamenu_delete_data" />

                            <input type='submit' class='button button-secondary confirm' value='<?php _e("Delete Data", "megamenu"); ?>' />
                        </form>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Export Theme", "megamenu"); ?>
                        <div class='mega-description'><?php _e("Export a menu theme", "megamenu"); ?></div>
                    </td>
                    <td class='mega-value'>
                        <form method="post" action="<?php admin_url( "admin.php?page=maxmegamenu&tab=tools") ?>">

                            <?php

                            if ( isset( $_POST['theme_export'] ) ) {

                                $theme_to_export = $_POST['theme_export'];


                                if ( isset( $this->themes[ $theme_to_export ] ) ) {

                                    echo "<textarea>" . htmlentities( json_encode( $this->themes[ $theme_to_export ] ) ) . "</textarea>";

                                }
                            } else {

                                echo "<select name='theme_export'>";
                                foreach ( $this->themes as $id => $theme ) {
                                    echo "<option value='{$id}'>{$theme['title']}</option>";
                                }
                                echo "</select>";

                                echo "<input type='submit' class='button button-secondary' value='" . __("Export Theme", "megamenu") . "' />";

                            }

                            ?>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Import Theme", "megamenu"); ?>
                        <div class='mega-description'><?php _e("Import a menu theme", "megamenu"); ?></div>
                    </td>
                    <td class='mega-value'>
                       <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                            <?php wp_nonce_field( 'megamenu_import_theme' ); ?>
                            <input type="hidden" name="action" value="megamenu_import_theme" />
                            <textarea name='data'></textarea>
                            <input type='submit' class='button button-secondary' value='<?php _e("Import Theme", "megamenu"); ?>' />
                        </form>
                    </td>
                </tr>
            </table>
        </div>

        <?php
    }


    /**
     * Main settings page wrapper.
     *
     * @since 1.4
     */
    public function page() {

        $tab = isset( $_GET['page'] ) ? substr( $_GET['page'], 12 ) : false;

        // backwards compatibility
        if ( isset ( $_GET['tab'] ) ) {
            $tab = $_GET['tab'];
        }

        if ( ! $tab ) {
            $tab = 'general_settings';
        }

        $header_links = apply_filters( "megamenu_header_links", array(
            'homepage' => array(
                'url' => 'https://www.maxmegamenu.com/?utm_source=free&amp;utm_medium=link&amp;utm_campaign=pro',
                'target' => '_mmmpro',
                'text' => __("Homepage", "megamenu"),
                'class' => ''
            ),
            'documentation' => array(
                'url' => 'https://www.maxmegamenu.com/documentation/getting-started/installation/?utm_source=free&amp;utm_medium=link&amp;utm_campaign=pro',
                'text' => __("Documentation", "megamenu"),
                'target' => '_mmmpro',
                'class' => ''
            )
        ) );

        if ( ! is_plugin_active('megamenu-pro/megamenu-pro.php') ) {
            $header_links['pro'] = array(
                'url' => 'https://www.maxmegamenu.com/upgrade/?utm_source=free&amp;utm_medium=link&amp;utm_campaign=pro',
                'target' => '_mmmpro',
                'text' => __("Upgrade to Pro - $19", "megamenu"),
                'class' => 'mega-highlight'
            );
        }

        $versions = apply_filters( "megamenu_versions", array(
            'core' => array(
                'version' => MEGAMENU_VERSION,
                'text' => __("Core version", "megamenu")
            ),
            'pro' => array(
                'version' => "<a href='https://www.maxmegamenu.com/upgrade/?utm_source=free&amp;utm_medium=link&amp;utm_campaign=pro' target='_mmmpro'>not installed</a>",
                'text' => __("Pro extension", "megamenu")
            )
        ) );

        ?>

        <div class='megamenu_outer_wrap'>
            <div class='megamenu_header_top'>
                <ul>
                    <?php
                        foreach ( $header_links as $id => $data ) {
                            echo "<li class='{$data['class']}'><a href='{$data['url']}' target='{$data['target']}'>{$data['text']}";
                            echo "</a>";
                            echo "</li>";
                        }
                    ?>
                </ul>
            </div>
            <div class='megamenu_header'>
                <div class='megamenu_header_left'>
                    <h2><?php _e("Max Mega Menu", "megamenu"); ?></h2>
                    <div class='version'>
                        <?php

                            $total = count( $versions );
                            $count = 0;
                            $separator = ' - ';

                            foreach ( $versions as $id => $data ) {
                                echo $data['text'] . ": <b>" . $data['version'] . "</b>";

                                $count = $count + 1;

                                if ( $total > 0 && $count != $total ) {
                                    echo $separator;
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class='megamenu_wrap'>
                <div class='megamenu_right'>
                    <?php $this->print_messages(); ?>

                    <?php

                        $saved_settings = get_option("megamenu_settings");

                        if ( has_action( "megamenu_page_{$tab}" ) ) {
                            do_action( "megamenu_page_{$tab}", $saved_settings );
                        } else {
                            do_action( "megamenu_page_general_settings", $saved_settings );
                        }

                    ?>
                </div>
            </div>

            <div class='megamenu_left'>
                <ul>
                    <?php

                        $tabs = apply_filters("megamenu_menu_tabs", array(
                            'general_settings' => __("General Settings", "megamenu"),
                            'theme_editor' => __("Menu Themes", "megamenu"),
                            'tools' => __("Tools", "megamenu")
                        ));

                        foreach ( $tabs as $key => $title ) {
                            $class = $tab == $key ? 'active' : '';


                            if ( $key == 'general_settings' ) {
                                $args = array( 'page' => 'maxmegamenu' );
                            } else {
                                $args = array( 'page' => 'maxmegamenu_' . $key );
                            }

                            $url = esc_url( add_query_arg( $args, admin_url("admin.php") ) );

                            echo "<li><a class='{$class}' href='{$url}'>{$title}</a></li>";
                        }

                    ?>
                </ul>
            </div>

        </div>

        <?php
    }


    /**
     * Display messages to the user
     *
     * @since 1.0
     */
    public function print_messages() {

        $this->init();

        $style_manager = new Mega_Menu_Style_Manager();

        $menu_id = 0;

        $menus = get_registered_nav_menus();

        if ( count( $menus ) ) {

            $locations = get_nav_menu_locations();

            foreach ($menus as $location => $description ) {

                if ( isset( $locations[ $location ] ) ) {

                    $menu_id = $locations[ $location ];
                    continue;

                }

            }

        }

        $test = $style_manager->generate_css_for_location( 'test', $this->active_theme, $menu_id );

        if ( is_wp_error( $test ) ) {
            echo "<p class='fail'>" . $test->get_error_message() . "</p>";
        }

        if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 'false' ) {
            echo "<p class='fail'>" . __("Failed to delete theme. The theme is in use by a menu.", "megamenu") . "</p>";
        }

        if ( isset( $_GET['clear_css_cache'] ) && $_GET['clear_css_cache'] == 'true' ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("CSS cache cleared", "megamenu") . "</p>";
        }

        if ( isset( $_GET['delete_data'] ) && $_GET['delete_data'] == 'true' ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("All plugin data removed", "megamenu") . "</p>";
        }

        if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 'true' ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("Theme Deleted", "megamenu") . "</p>";
        }

        if ( isset( $_GET['duplicated'] ) ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("Theme Duplicated", "megamenu") . "</p>";
        }

        if ( isset( $_GET['saved'] ) ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("Changes Saved", "megamenu") . "</p>";
        }

        if ( isset( $_GET['reverted'] ) ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("Theme Reverted", "megamenu") . "</p>";
        }

        if ( isset( $_GET['created'] ) ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("New Theme Created", "megamenu") . "</p>";
        }

        if ( isset( $_GET['add_location'] ) ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("New Menu Location Created", "megamenu") . "</p>";
        }

        if ( isset( $_GET['delete_location'] ) ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("Menu Location Deleted", "megamenu") . "</p>";
        }

        if ( isset( $_GET['theme_imported'] ) && $_GET['theme_imported'] == 'true' ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("Theme Imported", "megamenu") . "</p>";
        }

        if ( isset( $_GET['theme_imported'] ) && $_GET['theme_imported'] == 'false' ) {
            echo "<p class='fail'>" . __("Theme Import Failed", "megamenu") . "</p>";
        }

        if ( isset( $_POST['theme_export'] ) ) {
            echo "<p class='success'><i class='dashicons dashicons-yes'></i>" . __("Theme Exported", "megamenu") . "</p>";
        }

        do_action("megamenu_print_messages");

    }


    /**
     * Lists the available themes
     *
     * @since 1.0
     */
    public function theme_selector() {

        $list_items = "<select id='theme_selector'>";

        foreach ( $this->themes as $id => $theme ) {

            $locations = $this->theme_is_being_used_by_location( $id );

            $selected = $id == $this->id ? 'selected=selected' : '';

            $list_items .= "<option {$selected} value='" . admin_url("admin.php?page=maxmegamenu_theme_editor&theme={$id}") . "'>";
            $list_items .= $theme['title'];

            if ( is_array( $locations ) ) {
                $list_items .= " (" . implode( ", ", $locations ) . ")";
            }

            $list_items .= "</option>";
        }

        return $list_items .= "</select>";

    }

    /**
     * Checks to see if a given string contains any of the provided search terms
     *
     * @param srgin $key
     * @param array $needles
     * @since 1.0
     */
    private function string_contains( $key, $needles ) {

        foreach ( $needles as $needle ) {

            if ( strpos( $key, $needle ) !== FALSE ) {
                return true;
            }
        }

        return false;

    }


    /**
     * Displays the theme editor form.
     *
     * @since 1.0
     */
    public function theme_editor_page( $saved_settings ) {

        $this->init();

        $create_url = esc_url( add_query_arg(
            array(
                'action'=>'megamenu_add_theme'
            ),
            wp_nonce_url( admin_url("admin-post.php"), 'megamenu_create_theme' )
        ) );

        $duplicate_url = esc_url( add_query_arg(
            array(
                'action'=>'megamenu_duplicate_theme',
                'theme_id' => $this->id
            ),
            wp_nonce_url( admin_url("admin-post.php"), 'megamenu_duplicate_theme' )
        ) );

        $delete_url = esc_url( add_query_arg(
            array(
                'action'=>'megamenu_delete_theme',
                'theme_id' => $this->id
            ),
            wp_nonce_url( admin_url("admin-post.php"), 'megamenu_delete_theme' )
        ) );

        $revert_url = esc_url( add_query_arg(
            array(
                'action'=>'megamenu_revert_theme',
                'theme_id' => $this->id
            ),
            wp_nonce_url( admin_url("admin-post.php"), 'megamenu_revert_theme' )
        ) );

        ?>

        <div class='menu_settings'>

            <div class='theme_selector'>
                <?php _e("Select theme to edit", "megamenu"); ?> <?php echo $this->theme_selector(); ?> <?php _e("or", "megamenu"); ?>
                <a href='<?php echo $create_url ?>'><?php _e("create a new theme", "megamenu"); ?></a> <?php _e("or", "megamenu"); ?>
                <a href='<?php echo $duplicate_url ?>'><?php _e("duplicate this theme", "megamenu"); ?></a>
            </div>

            <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" class="theme_editor">
                <input type="hidden" name="theme_id" value="<?php echo $this->id; ?>" />
                <input type="hidden" name="action" value="megamenu_save_theme" />
                <?php wp_nonce_field( 'megamenu_save_theme' ); ?>

                <?php

                    $settings = apply_filters( 'megamenu_theme_editor_settings', array(
                        'general' => array(
                            'title' => __( "General Theme Settings", "megamenu" ),
                            'settings' => array(
                                'title' => array(
                                    'title' => __( "Theme Title", "megamenu" ),
                                    'description' => __( "", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'title'
                                        )
                                    )
                                ),
                                'arrow' => array(
                                    'title' => __( "Arrow", "megamenu" ),
                                    'description' => __( "Select the arrow styles.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Up", "megamenu" ),
                                            'type' => 'arrow',
                                            'key' => 'arrow_up'
                                        ),
                                        array(
                                            'title' => __( "Down", "megamenu" ),
                                            'type' => 'arrow',
                                            'key' => 'arrow_down'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'arrow',
                                            'key' => 'arrow_left'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'arrow',
                                            'key' => 'arrow_right'
                                        )
                                    )
                                ),
                                'line_height' => array(
                                    'title' => __( "Line Height", "megamenu" ),
                                    'description' => __( "Set the general line height to use in the panel contents.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'line_height'
                                        )
                                    )
                                ),
                                'z_index' => array(
                                    'title' => __( "Z Index", "megamenu" ),
                                    'description' => __( "Set the z-index to ensure the panels appear ontop of other content.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'z_index',
                                            'validation' => 'int'
                                        )
                                    )
                                ),
                                'shadow' => array(
                                    'title' => __( "Shadow", "megamenu" ),
                                    'description' => __( "Apply a shadow to mega and flyout menus.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Enabled", "megamenu" ),
                                            'type' => 'checkbox',
                                            'key' => 'shadow'
                                        ),
                                        array(
                                            'title' => __( "Horizontal", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'shadow_horizontal',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Vertical", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'shadow_vertical',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Blur", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'shadow_blur',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Spread", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'shadow_spread',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'shadow_color'
                                        )
                                    )
                                ),
                                'transitions' => array(
                                    'title' => __( "Hover Transitions", "megamenu" ),
                                    'description' => __( "Apply hover transitions to menu items. Note: Transitions will not apply to gradient backgrounds.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Enabled", "megamenu" ),
                                            'type' => 'checkbox',
                                            'key' => 'transitions'
                                        )
                                    )
                                ),
                                'resets' => array(
                                    'title' => __( "Reset Widget Styling", "megamenu" ),
                                    'description' => __( "Reset the styling of widgets within the mega menu?", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Enabled", "megamenu" ),
                                            'type' => 'checkbox',
                                            'key' => 'resets'
                                        )
                                    )
                                )
                            )
                        ),
                        'menu_bar' => array(
                            'title' => __( "Menu Bar", "megamenu" ),
                            'settings' => array(
                                'menu_background' => array(
                                    'title' => __( "Menu Background", "megamenu" ),
                                    'description' => __( "The background color for the main menu bar. Set each value to transparent for a 'button' style menu.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'container_background_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'container_background_to'
                                        )
                                    )
                                ),
                                'menu_padding' => array(
                                    'title' => __( "Menu Padding", "megamenu" ),
                                    'description' => __( "Padding for the main menu bar.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'container_padding_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'container_padding_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'container_padding_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'container_padding_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'menu_border_radius' => array(
                                    'title' => __( "Menu Border Radius", "megamenu" ),
                                    'description' => __( "Set a border radius on the main menu bar.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'container_border_radius_top_left',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Top Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'container_border_radius_top_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'container_border_radius_bottom_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'container_border_radius_bottom_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'menu_item_align' => array(
                                    'title' => __( "Menu Items Align", "megamenu" ),
                                    'description' => __( "Align <i>all</i> menu items to the left (default), centrally or to the right.", "megamenu" ),
                                    'info' => array( __( "This option will apply to all menu items. To align an individual menu item to the right, edit the menu item itself and set 'Menu Item Align' to 'Right'.", "megamenu" ) ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'align',
                                            'key' => 'menu_item_align'
                                        )
                                    )
                                ),
                                'top_level_menu_items' => array(
                                    'title' => __( "Top Level Menu Items", "megamenu" ),
                                    'description' => '',
                                ),
                                'menu_item_background' => array(
                                    'title' => __( "Menu Item Background", "megamenu" ),
                                    'description' => __( "The background color for each top level menu item. Tip: Set these values to transparent if you've already set a background color on the menu bar.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'menu_item_background_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'menu_item_background_to'
                                        )
                                    )
                                ),
                                'menu_item_background_hover' => array(
                                    'title' => __( "Menu Item Background (Hover)", "megamenu" ),
                                    'description' => __( "The background color for a top level menu item (on hover).", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'menu_item_background_hover_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'menu_item_background_hover_to'
                                        )
                                    )
                                ),
                                'menu_item_spacing' => array(
                                    'title' => __( "Menu Item Spacing", "megamenu" ),
                                    'description' => __( "Define the size of the gap between each top level menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_spacing',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'menu_item_height' => array(
                                    'title' => __( "Menu Item Height", "megamenu" ),
                                    'description' => __( "Define the height of each top level menu item. This value, plus the container top and bottom padding values define the overall height of the menu bar.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_height',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'menu_item_font' => array(
                                    'title' => __( "Font", "megamenu" ),
                                    'description' => __( "The font to use for each top level menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'menu_item_link_color'
                                        ),
                                        array(
                                            'title' => __( "Size", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_font_size',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Family", "megamenu" ),
                                            'type' => 'font',
                                            'key' => 'menu_item_link_font'
                                        ),
                                        array(
                                            'title' => __( "Transform", "megamenu" ),
                                            'type' => 'transform',
                                            'key' => 'menu_item_link_text_transform'
                                        ),
                                        array(
                                            'title' => __( "Weight", "megamenu" ),
                                            'type' => 'weight',
                                            'key' => 'menu_item_link_weight'
                                        ),
                                        array(
                                            'title' => __( "Decoration", "megamenu" ),
                                            'type' => 'decoration',
                                            'key' => 'menu_item_link_text_decoration'
                                        ),
                                    )
                                ),
                                'menu_item_font_hover' => array(
                                    'title' => __( "Font (Hover)", "megamenu" ),
                                    'description' => __( "Set the font to use for each top level menu item (on hover).", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'menu_item_link_color_hover'
                                        ),
                                        array(
                                            'title' => __( "Weight", "megamenu" ),
                                            'type' => 'weight',
                                            'key' => 'menu_item_link_weight_hover'
                                        ),
                                        array(
                                            'title' => __( "Decoration", "megamenu" ),
                                            'type' => 'decoration',
                                            'key' => 'menu_item_link_text_decoration_hover'
                                        ),
                                    )
                                ),
                                'menu_item_padding' => array(
                                    'title' => __( "Menu Item Padding", "megamenu" ),
                                    'description' => __( "Set the padding for each top level menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_padding_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_padding_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_padding_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_padding_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'menu_item_border' => array(
                                    'title' => __( "Menu Item Border", "megamenu" ),
                                    'description' => __( "Set the border to display on each top level menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'menu_item_border_color'
                                        ),
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_border_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_border_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_border_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_border_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'menu_item_border_hover' => array(
                                    'title' => __( "Menu Item Border (Hover)", "megamenu" ),
                                    'description' => __( "Set the hover border color.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'menu_item_border_color_hover'
                                        )
                                    )
                                ),
                                'menu_item_border_radius' => array(
                                    'title' => __( "Menu Item Border Radius", "megamenu" ),
                                    'description' => __( "Set rounded corners for each top level menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_border_radius_top_left',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Top Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_border_radius_top_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_border_radius_bottom_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_link_border_radius_bottom_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'menu_item_divider' => array(
                                    'title' => __( "Menu Item Divider", "megamenu" ),
                                    'description' => __( "Show a small divider bar between each menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Enabled", "megamenu" ),
                                            'type' => 'checkbox',
                                            'key' => 'menu_item_divider'
                                        ),
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'menu_item_divider_color'
                                        ),
                                        array(
                                            'title' => __( "Glow Opacity", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'menu_item_divider_glow_opacity',
                                            'validation' => 'float'
                                        )
                                    )
                                ),
                                'menu_item_highlight' => array(
                                    'title' => __( "Highlight Current Item", "megamenu" ),
                                    'description' => __( "Apply the 'hover' styling to current menu items.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Enabled", "megamenu" ),
                                            'type' => 'checkbox',
                                            'key' => 'menu_item_highlight_current'
                                        )
                                    )
                                )
                            )
                        ),
                        'mega_panels' => array(
                            'title' => __( "Mega Menus", "megamenu" ),
                            'settings' => array(
                                'panel_background' => array(
                                    'title' => __( "Panel Background", "megamenu" ),
                                    'description' => __( "Set a background color for a whole panel.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_background_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_background_to'
                                        )
                                    )
                                ),
                                'panel_width' => array(
                                    'title' => __( "Panel Width", "megamenu" ),
                                    'description' => __( "Mega Panel width.", "megamenu" ),
                                    'info' => array(
                                        __("A 100% wide panel will only ever be as wide as the menu itself. For a fixed panel width set this to a pixel value.", "megamenu"),
                                        __("Advanced: Enter a jQuery selector to synchronize the width and position of the sub menu with existing page element (e.g. body, #container, .page).", "megamenu")
                                    ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Enabled", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_width'
                                        )
                                    )
                                ),
                                'panel_padding' => array(
                                    'title' => __( "Panel Padding", "megamenu" ),
                                    'description' => __( "Set the padding for the whole panel. Set these values 0px if you wish your panel content to go edge-to-edge.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_padding_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_padding_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_padding_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_padding_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'panel_border' => array(
                                    'title' => __( "Panel Border", "megamenu" ),
                                    'description' => __( "Set the border to display on the Mega Panel.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_border_color'
                                        ),
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_border_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_border_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_border_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_border_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'panel_border_radius' => array(
                                    'title' => __( "Panel Border Radius", "megamenu" ),
                                    'description' => __( "Set rounded corners for the panel.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_border_radius_top_left',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Top Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_border_radius_top_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_border_radius_bottom_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_border_radius_bottom_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'panel_padding' => array(
                                    'title' => __( "Widget Padding", "megamenu" ),
                                    'description' => __( "Use this to define the spacing between each widget in the panel.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_widget_padding_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_widget_padding_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_widget_padding_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_widget_padding_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'widget_heading_font' => array(
                                    'title' => __( "Widget Heading Font", "megamenu" ),
                                    'description' => __( "Set the font to use Widget headers in the mega menu. Tip: set this to the same style as the Second Level Menu Item Header font to keep your styling consistent.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_header_color'
                                        ),
                                        array(
                                            'title' => __( "Size", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_font_size',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Family", "megamenu" ),
                                            'type' => 'font',
                                            'key' => 'panel_header_font'
                                        ),
                                        array(
                                            'title' => __( "Transform", "megamenu" ),
                                            'type' => 'transform',
                                            'key' => 'panel_header_text_transform'
                                        ),
                                        array(
                                            'title' => __( "Weight", "megamenu" ),
                                            'type' => 'weight',
                                            'key' => 'panel_header_font_weight'
                                        ),
                                        array(
                                            'title' => __( "Decoration", "megamenu" ),
                                            'type' => 'decoration',
                                            'key' => 'panel_header_text_decoration'
                                        ),
                                    )
                                ),
                                'widget_content_font' => array(
                                    'title' => __( "Widget Content Font", "megamenu" ),
                                    'description' => __( "Set the font to use for panel contents.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_font_color'
                                        ),
                                        array(
                                            'title' => __( "Size", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_font_size',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Family", "megamenu" ),
                                            'type' => 'font',
                                            'key' => 'panel_font_family'
                                        )
                                    )
                                ),
                                'widget_heading_padding' => array(
                                    'title' => __( "Widget Padding", "megamenu" ),
                                    'description' => __( "Set the padding for the widget headings.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_padding_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_padding_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_padding_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_padding_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'widget_heading_margin' => array(
                                    'title' => __( "Widget Padding", "megamenu" ),
                                    'description' => __( "Set the margin for the widget headings.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_margin_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_margin_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_margin_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_margin_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'widget_header_border' => array(
                                    'title' => __( "Widget Header Border", "megamenu" ),
                                    'description' => __( "Set the border for the widget headings.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_header_border_color'
                                        ),
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_border_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_border_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_border_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_header_border_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'second_level_menu_items' => array(
                                    'title' => __( "Second Level Menu Items", "megamenu" ),
                                    'description' => '',
                                ),
                                'second_level_font' => array(
                                    'title' => __( "Font", "megamenu" ),
                                    'description' => __( "Set the font for second level menu items when they're displayed in a Mega Menu.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_second_level_font_color'
                                        ),
                                        array(
                                            'title' => __( "Size", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_font_size',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Family", "megamenu" ),
                                            'type' => 'font',
                                            'key' => 'panel_second_level_font'
                                        ),
                                        array(
                                            'title' => __( "Transform", "megamenu" ),
                                            'type' => 'transform',
                                            'key' => 'panel_second_level_text_transform'
                                        ),
                                        array(
                                            'title' => __( "Weight", "megamenu" ),
                                            'type' => 'weight',
                                            'key' => 'panel_second_level_font_weight'
                                        ),
                                        array(
                                            'title' => __( "Decoration", "megamenu" ),
                                            'type' => 'decoration',
                                            'key' => 'panel_second_level_text_decoration'
                                        ),
                                    )
                                ),
                                'second_level_font_hover' => array(
                                    'title' => __( "Font (Hover)", "megamenu" ),
                                    'description' => __( "Set the font style on hover.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_second_level_font_color_hover'
                                        ),
                                        array(
                                            'title' => __( "Weight", "megamenu" ),
                                            'type' => 'weight',
                                            'key' => 'panel_second_level_font_weight_hover'
                                        ),
                                        array(
                                            'title' => __( "Decoration", "megamenu" ),
                                            'type' => 'decoration',
                                            'key' => 'panel_second_level_text_decoration_hover'
                                        ),
                                    )
                                ),
                                'second_level_background_hover' => array(
                                    'title' => __( "Background (Hover)", "megamenu" ),
                                    'description' => __( "Set the background hover color for second level menu items.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_second_level_background_hover_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_second_level_background_hover_to'
                                        )
                                    )
                                ),
                                'second_level_padding' => array(
                                    'title' => __( "Padding", "megamenu" ),
                                    'description' => __( "Set the padding for the second level menu items.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_padding_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_padding_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_padding_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_padding_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'second_level_margin' => array(
                                    'title' => __( "Margin", "megamenu" ),
                                    'description' => __( "Set the margin for the second level menu items.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_margin_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_margin_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_margin_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_margin_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'second_level_border' => array(
                                    'title' => __( "Border", "megamenu" ),
                                    'description' => __( "Set the border for the second level menu items.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_second_level_border_color'
                                        ),
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_border_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_border_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_border_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_second_level_border_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'third_level_menu_items' => array(
                                    'title' => __( "Third Level Menu Items", "megamenu" ),
                                    'description' => '',
                                ),
                                'third_level_font' => array(
                                    'title' => __( "Font", "megamenu" ),
                                    'description' => __( "Set the font for third level menu items when they're displayed in a Mega Menu.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_third_level_font_color'
                                        ),
                                        array(
                                            'title' => __( "Size", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_third_level_font_size',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Family", "megamenu" ),
                                            'type' => 'font',
                                            'key' => 'panel_third_level_font'
                                        ),
                                        array(
                                            'title' => __( "Transform", "megamenu" ),
                                            'type' => 'transform',
                                            'key' => 'panel_third_level_text_transform'
                                        ),
                                        array(
                                            'title' => __( "Weight", "megamenu" ),
                                            'type' => 'weight',
                                            'key' => 'panel_third_level_font_weight'
                                        ),
                                        array(
                                            'title' => __( "Decoration", "megamenu" ),
                                            'type' => 'decoration',
                                            'key' => 'panel_third_level_text_decoration'
                                        ),
                                    )
                                ),
                                'third_level_font_hover' => array(
                                    'title' => __( "Font (Hover)", "megamenu" ),
                                    'description' => __( "Set the font style on hover.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_third_level_font_color_hover'
                                        ),
                                        array(
                                            'title' => __( "Weight", "megamenu" ),
                                            'type' => 'weight',
                                            'key' => 'panel_third_level_font_weight_hover'
                                        ),
                                        array(
                                            'title' => __( "Decoration", "megamenu" ),
                                            'type' => 'decoration',
                                            'key' => 'panel_third_level_text_decoration_hover'
                                        ),
                                    )
                                ),
                                'third_level_background_hover' => array(
                                    'title' => __( "Background (Hover)", "megamenu" ),
                                    'description' => __( "Set the background hover color for third level menu items.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_third_level_background_hover_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'panel_third_level_background_hover_to'
                                        )
                                    )
                                ),
                                'third_level_padding' => array(
                                    'title' => __( "Padding", "megamenu" ),
                                    'description' => __( "Set the padding for the third level menu items.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_third_level_padding_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_third_level_padding_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_third_level_padding_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'panel_third_level_padding_left',
                                            'validation' => 'px'
                                        )
                                    )
                                )
                            )
                        ),
                        'flyout_menus' => array(
                            'title' => __( "Flyout Menus", "megamenu"),
                            'settings' => array(
                                'flyout_menu_background' => array(
                                    'title' => __( "Menu Background", "megamenu" ),
                                    'description' => __( "Set the background color for the flyout menu.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_menu_background_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_menu_background_to'
                                        )
                                    )
                                ),
                                'flyout_menu_width' => array(
                                    'title' => __( "Menu Width", "megamenu" ),
                                    'description' => __( "The width of each flyout menu. This must be a fixed pixel value.", "megamenu" ),
                                    'info' => array( __( "Set this value to the width of your longest menu item title to stop menu items wrapping onto 2 lines.", "megamenu" ) ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_width',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'flyout_menu_padding' => array(
                                    'title' => __( "Menu Padding", "megamenu" ),
                                    'description' => __( "Set the padding for the whole flyout menu.", "megamenu" ),
                                    'info' => array( __( "Only suitable for single level flyout menus. If you're using multi level flyout menus set these values to 0px.", "megamenu" ) ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_padding_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_padding_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_padding_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_padding_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'flyout_menu_border' => array(
                                    'title' => __( "Menu Border", "megamenu" ),
                                    'description' => __( "Set the border for the flyout menu.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_border_color'
                                        ),
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_border_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_border_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_border_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_border_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'flyout_menu_border_radius' => array(
                                    'title' => __( "Menu Border Radius", "megamenu" ),
                                    'description' => __( "Set rounded corners for flyout menus. Rounded corners will be applied to all flyout menu levels.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_border_radius_top_left',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Top Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_border_radius_top_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_border_radius_bottom_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_border_radius_bottom_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'flyout_menu_item_background' => array(
                                    'title' => __( "Item Background", "megamenu" ),
                                    'description' => __( "Set the background color for a flyout menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_background_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_background_to'
                                        )
                                    )
                                ),
                                'flyout_menu_item_background_hover' => array(
                                    'title' => __( "Item Background (Hover)", "megamenu" ),
                                    'description' => __( "Set the background color for a flyout menu item (on hover).", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_background_hover_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_background_hover_to'
                                        )
                                    )
                                ),
                                'flyout_menu_item_height' => array(
                                    'title' => __( "Item Height", "megamenu" ),
                                    'description' => __( "The height of each flyout menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_link_height',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'flyout_menu_item_padding' => array(
                                    'title' => __( "Item Padding", "megamenu" ),
                                    'description' => __( "Set the padding for each flyout menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Top", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_link_padding_top',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Right", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_link_padding_right',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Bottom", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_link_padding_bottom',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Left", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_link_padding_left',
                                            'validation' => 'px'
                                        )
                                    )
                                ),
                                'flyout_menu_item_font' => array(
                                    'title' => __( "Item Font", "megamenu" ),
                                    'description' => __( "Set the font for the flyout menu items.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_link_color'
                                        ),
                                        array(
                                            'title' => __( "Size", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'flyout_link_size',
                                            'validation' => 'px'
                                        ),
                                        array(
                                            'title' => __( "Family", "megamenu" ),
                                            'type' => 'font',
                                            'key' => 'flyout_link_family'
                                        ),
                                        array(
                                            'title' => __( "Transform", "megamenu" ),
                                            'type' => 'transform',
                                            'key' => 'flyout_link_text_transform'
                                        ),
                                        array(
                                            'title' => __( "Weight", "megamenu" ),
                                            'type' => 'weight',
                                            'key' => 'flyout_link_weight'
                                        ),
                                        array(
                                            'title' => __( "Decoration", "megamenu" ),
                                            'type' => 'decoration',
                                            'key' => 'flyout_link_text_decoration'
                                        ),
                                    )
                                ),
                                'flyout_menu_item_font_hover' => array(
                                    'title' => __( "Item Font (Hover)", "megamenu" ),
                                    'description' => __( "Set the font for the flyout menu items.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_link_color_hover'
                                        ),
                                        array(
                                            'title' => __( "Weight", "megamenu" ),
                                            'type' => 'weight',
                                            'key' => 'flyout_link_weight_hover'
                                        ),
                                        array(
                                            'title' => __( "Decoration", "megamenu" ),
                                            'type' => 'decoration',
                                            'key' => 'flyout_link_text_decoration_hover'
                                        ),
                                    )
                                ),
                                'flyout_menu_item_divider' => array(
                                    'title' => __( "Item Divider", "megamenu" ),
                                    'description' => __( "Show a line divider below each menu item.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "Enabled", "megamenu" ),
                                            'type' => 'checkbox',
                                            'key' => 'flyout_menu_item_divider'
                                        ),
                                        array(
                                            'title' => __( "Color", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'flyout_menu_item_divider_color'
                                        )
                                    )
                                ),
                            )
                        ),
                        'mobile_menu' => array(
                            'title' => __( "Mobile Menu", "megamenu" ),
                            'settings' => array(
                                'responsive_breakpoint' => array(
                                    'title' => __( "Responsive Breakpoint", "megamenu" ),
                                    'description' => __( "Set the width at which the menu turns into a mobile menu. Set to 0px to disable responsive menu.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'responsive_breakpoint',
                                            'validation' => 'px'
                                        )
                                    ),
                                ),
                                'responsive_text' => array(
                                    'title' => __( "Toggle Bar Text", "megamenu" ),
                                    'description' => __( "Text to display next to the mobile toggle icon.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'responsive_text'
                                        )
                                    )
                                ),
                                'toggle_font' => array(
                                    'title' => __( "Toggle Bar Text Color", "megamenu" ),
                                    'description' => __( "Set the font color for the toggle bar.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'toggle_font_color'
                                        )
                                    )
                                ),
                                'toggle_bar_background' => array(
                                    'title' => __( "Toggle Bar Background", "megamenu" ),
                                    'description' => __( "Set the background color for the mobile menu toggle bar.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "From", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'toggle_background_from'
                                        ),
                                        array(
                                            'title' => __( "To", "megamenu" ),
                                            'type' => 'color',
                                            'key' => 'toggle_background_to'
                                        )
                                    )
                                ),
                                'mobile_columns' => array(
                                    'title' => __( "Mega Menu Columns", "megamenu" ),
                                    'description' => __( "Number of columns to display widgets/second level menu items in.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'mobile_columns',
                                            'key' => 'mobile_columns'
                                        )
                                    )
                                ),
                                'mobile_menu_item_height' => array(
                                    'title' => __( "Menu Item Height", "megamenu" ),
                                    'description' => __( "Height of each top level item in the mobile menu.", "megamenu" ),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'freetext',
                                            'key' => 'mobile_menu_item_height'
                                        )
                                    )
                                )
                            )
                        ),
                        'custom_styling' => array(
                            'title' => __( "Custom Styling", "megamenu"),
                            'settings' => array(
                                'custom_styling' => array(
                                    'title' => __( "CSS Editor", "megamenu" ),
                                    'description' => __( "Define any custom CSS you wish to add to menus using this theme. You can use standard CSS or SCSS.", "megamenu"),
                                    'settings' => array(
                                        array(
                                            'title' => __( "", "megamenu" ),
                                            'type' => 'textarea',
                                            'key' => 'custom_css'
                                        )
                                    )
                                )
                            )
                        ),
                    ) );

                    echo "<div class='accordion-container'>";
                    echo "<ul class='outer-border'>";

                    $is_first = true;

                    foreach ( $settings as $section_id => $section ) {

                        $open = $is_first ? 'open' : '';

                        echo "    <li class='control-section accordion-section {$open} mega-{$section_id}'>";
                        echo "        <h4 class='accordion-section-title hndle'>".$section['title'] . "</h4>";
                        echo "        <div class='accordion-section-content '>";
                        echo "            <table class='{$section_id}'>";

                        $is_first = false;

                        foreach ( $section['settings'] as $group_id => $group ) {

                            echo "<tr class='mega-{$group_id}'>";

                            if ( isset( $group['settings'] ) ) {

                                echo "<td class='mega-name'>" . $group['title'] . "<div class='mega-description'>" . $group['description'] . "</div></td>";
                                echo "<td class='mega-value'>";

                                foreach ( $group['settings'] as $setting_id => $setting ) {

                                    if ( isset( $setting['validation'] ) ) {
                                        echo "<label class='mega-{$setting['key']}' data-validation='{$setting['validation']}'>";
                                    } else {
                                        echo "<label class='mega-{$setting['key']}'>";
                                    }
                                    echo "<span class='mega-short-desc'>{$setting['title']}</span>";

                                    switch ( $setting['type'] ) {
                                        case "freetext":
                                            $this->print_theme_freetext_option( $setting['key'] );
                                            break;
                                        case "textarea":
                                            $this->print_theme_textarea_option( $setting['key'] );
                                            break;
                                        case "align":
                                            $this->print_theme_align_option( $setting['key'] );
                                            break;
                                        case "checkbox":
                                            $this->print_theme_checkbox_option( $setting['key'] );
                                            break;
                                        case "arrow":
                                            $this->print_theme_arrow_option( $setting['key'] );
                                            break;
                                        case "color":
                                            $this->print_theme_color_option( $setting['key'] );
                                            break;
                                        case "weight":
                                            $this->print_theme_weight_option( $setting['key'] );
                                            break;
                                        case "font":
                                            $this->print_theme_font_option( $setting['key'] );
                                            break;
                                        case "transform":
                                            $this->print_theme_transform_option( $setting['key'] );
                                            break;
                                        case "decoration":
                                            $this->print_theme_text_decoration_option( $setting['key'] );
                                            break;
                                        case "mobile_columns":
                                            $this->print_theme_mobile_columns_option( $setting['key'] );
                                            break;
                                    }

                                    echo "</label>";

                                }

                                if ( isset( $group['info'] ) ) {
                                    foreach ( $group['info'] as $paragraph ) {
                                        echo "<div class='mega-info'>{$paragraph}</div>";
                                    }
                                }

                                foreach ( $group['settings'] as $setting_id => $setting ) {
                                    if ( isset( $setting['validation'] ) ) {

                                        echo "<div class='mega-validation-message mega-validation-message-{$setting['key']}'>";

                                        if ( $setting['validation'] == 'int' ) {
                                            $message = __("Enter a whole number (e.g. 1, 5, 100, 999)");
                                        }

                                        if ( $setting['validation'] == 'px' ) {
                                            $message = __("Enter a value including a unit (e.g. 10px, 10rem, 10%)");
                                        }

                                        if ( $setting['validation'] == 'float' ) {
                                            $message = __("Enter a valid number (e.g. 0.1, 1, 10, 999)");
                                        }

                                        if ( strlen( $setting['title'] ) ) {
                                            echo $setting['title'] . ": " . $message;
                                        } else {
                                            echo $message;
                                        }

                                        echo "</div>";
                                    }
                                }

                                echo "</td>";
                            } else {
                                echo "<td colspan='2'><h5>{$group['title']}</h5></td>";
                            }

                            echo "</tr>";

                        }

                        echo "</table>";
                        echo "</div>";
                        echo "</li>";

                    }

                ?>
                    </ul>
                </div>

                <div class='megamenu_submit'>
                    <div class='mega_left'>
                        <?php submit_button(); ?>
                    </div>
                    <div class='mega_right'>
                        <?php if ( $this->string_contains( $this->id, array("custom") ) ) : ?>
                            <a class='delete confirm' href='<?php echo $delete_url; ?>'><?php _e("Delete Theme", "megamenu"); ?></a>
                        <?php else : ?>
                            <a class='revert confirm' href='<?php echo $revert_url; ?>'><?php _e("Revert Theme", "megamenu"); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <?php

    }


    /**
     * Print a select dropdown with left, center and right options
     *
     * @since 1.6.1
     * @param string $key
     * @param string $value
     */
    public function print_theme_align_option( $key ) {

        $value = $this->active_theme[$key];

        ?>

            <select name='settings[<?php echo $key ?>]'>
                <option value='left' <?php selected( $value, 'left' ); ?>><?php _e("Left", "megamenu") ?></option>
                <option value='center' <?php selected( $value, 'center' ); ?>><?php _e("Center", "megamenu") ?></option>
                <option value='right' <?php selected( $value, 'right' ); ?>><?php _e("Right", "megamenu") ?></option>
            </select>

        <?php
    }

    /**
     * Print a select dropdown with 1 and 2 options
     *
     * @since 1.2.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_mobile_columns_option( $key ) {

        $value = $this->active_theme[$key];

        ?>

            <select name='settings[<?php echo $key ?>]'>
                <option value='1' <?php selected( $value, '1' ); ?>><?php _e("1 Column", "megamenu") ?></option>
                <option value='2' <?php selected( $value, '2' ); ?>><?php _e("2 Columns", "megamenu") ?></option>
            </select>

        <?php
    }

    /**
     * Print a select dropdown with text decoration options
     *
     * @since 1.6.1
     * @param string $key
     * @param string $value
     */
    public function print_theme_text_decoration_option( $key ) {

        $value = $this->active_theme[$key];

        ?>

            <select name='settings[<?php echo $key ?>]'>
                <option value='none' <?php selected( $value, 'none' ); ?>><?php _e("None", "megamenu") ?></option>
                <option value='underline' <?php selected( $value, 'underline' ); ?>><?php _e("Underline", "megamenu") ?></option>
            </select>

        <?php
    }


    /**
     * Print a checkbox option
     *
     * @since 1.6.1
     * @param string $key
     * @param string $value
     */
    public function print_theme_checkbox_option( $key ) {

        $value = $this->active_theme[$key];

        ?>

            <input type='hidden' name='checkboxes[<?php echo $key ?>]' />
            <input type='checkbox' name='settings[<?php echo $key ?>]' <?php checked( $value, 'on' ); ?> />

        <?php
    }


    /**
     * Print an arrow dropdown selection box
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_arrow_option( $key ) {

        $value = $this->active_theme[$key];

        $arrow_icons = $this->arrow_icons();

        ?>
            <select class='icon_dropdown' name='settings[<?php echo $key ?>]'>
                <?php

                    echo "<option value='disabled'>" . __("Disabled", "megamenu") . "</option>";

                    foreach ($arrow_icons as $code => $class) {
                        $name = str_replace('dashicons-', '', $class);
                        $name = ucwords(str_replace(array('-','arrow'), ' ', $name));
                        echo "<option data-class='{$class}' value='{$code}' " . selected( $value == $code ) . ">{$name}</option>";
                    }

                ?>
            </select>
            <span class="selected_icon <?php echo $arrow_icons[$value] ?>"></span>

        <?php
    }


    /**
     * Print a colorpicker
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_color_option( $key ) {

        $value = $this->active_theme[$key];

        if ( $value == 'transparent' ) {
            $value = 'rgba(0,0,0,0)';
        }

        if ( $value == 'rgba(0,0,0,0)' ) {
            $value_text = 'transparent';
        } else {
            $value_text = $value;
        }

        echo "<div class='mm-picker-container'>";
        echo "    <input type='text' class='mm_colorpicker' name='settings[$key]' value='{$value}' />";
        echo "    <div class='chosen-color'>{$value_text}</div>";
        echo "</div>";

    }


    /**
     * Print a font weight selector
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_weight_option( $key ) {

        $value = $this->active_theme[$key];

        $options = apply_filters( "megamenu_font_weights", array(
            'inherit' => __("Theme Default", "megamenu"),
            '300' => __("Light (300)", "megamenu"),
            'normal' => __("Normal (400)", "megamenu"),
            'bold' => __("Bold (700)", "megamenu"),
        ) );

        /**
         *   '100' => __("Thin (100)", "megamenu"),
         *   '200' => __("Extra Light (200)", "megamenu"),
         *   '300' => __("Light (300)", "megamenu"),
         *   'normal' => __("Normal (400)", "megamenu"),
         *   '500' => __("Medium (500)", "megamenu"),
         *   '600' => __("Semi Bold (600)", "megamenu"),
         *   'bold' => __("Bold (700)", "megamenu"),
         *   '800' => __("Extra Bold (800)", "megamenu"),
         *   '900' => __("Black (900)", "megamenu")
        */

        echo "<select name='settings[$key]'>";

        foreach ( $options as $weight => $name ) {
            echo "<option value='{$weight}' " . selected( $value, $weight, true ) . ">{$name}</option>";
        }

        echo "</select>";

    }


    /**
     * Print a font transform selector
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_transform_option( $key ) {

        $value = $this->active_theme[$key];

        echo "<select name='settings[$key]'>";
        echo "    <option value='none' "      . selected( $value, 'none', true) . ">" . __("Normal", "megamenu") . "</option>";
        echo "    <option value='capitalize'" . selected( $value, 'capitalize', true) . ">" . __("Capitalize", "megamenu") . "</option>";
        echo "    <option value='uppercase'"  . selected( $value, 'uppercase', true) . ">" . __("Uppercase", "megamenu") . "</option>";
        echo "    <option value='lowercase'"  . selected( $value, 'lowercase', true) . ">" . __("Lowercase", "megamenu") . "</option>";
        echo "</select>";

    }


    /**
     * Print a textarea
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_textarea_option( $key ) {

        $value = $this->active_theme[$key];
        ?>

        <textarea id='codemirror' name='settings[<?php echo $key ?>]'><?php echo stripslashes( $value ) ?></textarea>

        <p><b><?php _e("Custom Styling Tips", "megamenu"); ?></b></p>
        <ul class='custom_styling_tips'>
            <li><code>#{$wrap}</code> <?php _e("converts to the ID selector of the menu wrapper, e.g. div#mega-menu-wrap-primary", "megamenu"); ?></li>
            <li><code>#{$menu}</code> <?php _e("converts to the ID selector of the menu, e.g. ul#mega-menu-primary", "megamenu"); ?></li>
            <li><?php _e("Use @import rules to import CSS from other plugins or your theme directory, e.g:"); ?>
            <br /><br /><code>#{$wrap} #{$menu} {<br />&nbsp;&nbsp;&nbsp;&nbsp;@import "shortcodes-ultimate/assets/css/box-shortcodes.css";<br />}</code></li>
        </ul>

        <?php

    }


    /**
     * Print a font selector
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_font_option( $key ) {

        $value = $this->active_theme[$key];

        echo "<select name='settings[$key]'>";

        echo "<option value='inherit'>" . __("Theme Default", "megamenu") . "</option>";

        foreach ( $this->fonts() as $font ) {
            $parts = explode(",", $font);
            $font_name = trim($parts[0]);
            echo "<option value=\"{$font}\" " . selected( $font, $value ) . ">{$font_name}</option>";
        }

        echo "</select>";
    }


    /**
     * Print a text input
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_freetext_option( $key ) {

        $value = $this->active_theme[$key];

        echo "<input class='mega-setting-{$key}' type='text' name='settings[$key]' value='{$value}' />";

    }


    /**
     * Returns a list of available fonts.
     *
     * @since 1.0
     */
    public function fonts() {

        $fonts = array(
            "Georgia, serif",
            "Palatino Linotype, Book Antiqua, Palatino, serif",
            "Times New Roman, Times, serif",
            "Arial, Helvetica, sans-serif",
            "Arial Black, Gadget, sans-serif",
            "Comic Sans MS, cursive, sans-serif",
            "Impact, Charcoal, sans-serif",
            "Lucida Sans Unicode, Lucida Grande, sans-serif",
            "Tahoma, Geneva, sans-serif",
            "Trebuchet MS, Helvetica, sans-serif",
            "Verdana, Geneva, sans-serif",
            "Courier New, Courier, monospace",
            "Lucida Console, Monaco, monospace"
        );

        $fonts = apply_filters( "megamenu_fonts", $fonts );

        return $fonts;

    }


    /**
     * List of all available arrow DashIcon classes.
     *
     * @since 1.0
     * @return array - Sorted list of icon classes
     */
    private function arrow_icons() {

        $icons = array(
            'dash-f142' => 'dashicons-arrow-up',
            'dash-f140' => 'dashicons-arrow-down',
            'dash-f139' => 'dashicons-arrow-right',
            'dash-f141' => 'dashicons-arrow-left',
            'dash-f342' => 'dashicons-arrow-up-alt',
            'dash-f346' => 'dashicons-arrow-down-alt',
            'dash-f344' => 'dashicons-arrow-right-alt',
            'dash-f340' => 'dashicons-arrow-left-alt',
            'dash-f343' => 'dashicons-arrow-up-alt2',
            'dash-f347' => 'dashicons-arrow-down-alt2',
            'dash-f345' => 'dashicons-arrow-right-alt2',
            'dash-f341' => 'dashicons-arrow-left-alt2',
        );

        $icons = apply_filters( "megamenu_arrow_icons", $icons );

        return $icons;

    }


    /**
     * Enqueue nav-menus.php scripts
     *
     * @since 1.8.3
     */
    public function enqueue_scripts() {
        wp_enqueue_script('accordion');

        wp_enqueue_style( 'spectrum', MEGAMENU_BASE_URL . 'js/spectrum/spectrum.css', false, MEGAMENU_VERSION );
        wp_enqueue_style( 'mega-menu-settings', MEGAMENU_BASE_URL . 'css/admin/settings.css', false, MEGAMENU_VERSION );
        wp_enqueue_style( 'codemirror', MEGAMENU_BASE_URL . 'js/codemirror/codemirror.css', false, MEGAMENU_VERSION );

        wp_enqueue_script( 'spectrum', MEGAMENU_BASE_URL . 'js/spectrum/spectrum.js', array( 'jquery' ), MEGAMENU_VERSION );
        wp_enqueue_script( 'codemirror', MEGAMENU_BASE_URL . 'js/codemirror/codemirror.js', array(), MEGAMENU_VERSION );
        wp_enqueue_script( 'mega-menu-theme-editor', MEGAMENU_BASE_URL . 'js/settings.js', array('jquery', 'spectrum', 'codemirror'), MEGAMENU_VERSION );

        wp_localize_script( 'mega-menu-theme-editor', 'megamenu_settings',
            array(
                'confirm' => __("Are you sure?", "megamenu")
            )
        );
    }

}

endif;