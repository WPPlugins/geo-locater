<?php
defined( 'ABSPATH' ) or die( 'Direct access to file is not allowed' );
/**
 * Locater Class
 *
 * Used for back end functionality
 * This class is responsibe for creating/managing custom post type, register menu & register custom setting variables etc.
 *
 */
class Locater {

/**
 * locater_menu
 *
 * Register menu in admin dashoard.
 *
 */
    public static function locater_menu() {
        add_submenu_page('edit.php?post_type=location', 'Locater Settings', 'Settings', 'manage_options', 'locater_settings', array('Locater', 'locater_settings'));
        add_submenu_page('edit.php?post_type=location', 'Locater Shortcode', 'Locater Shortcode', 'manage_options', 'locater_shortcode', array('Locater', 'locater_shortcode'));
        add_action('admin_init', array('Locater', 'register_locater_settings'));
    }


/**
 * register_post_type
 *
 * Responsible for creating a custom post type.
 *
 */
    public static function register_post_type() {

        $location_labels = array(
            'name' => _x('Locations', 'location'),
            'singular_name' => _x('location', 'location'),
            'add_new' => _x('Add New Location', 'location'),
            'add_new_item' => _x('Add New Location', 'location'),
            'edit_item' => _x('Edit Location', 'location'),
            'new_item' => _x('New Location', 'location'),
            'view_item' => _x('View Location', 'location'),
            'search_items' => _x('Search Location', 'location'),
            'not_found' => _x('No Location found', 'location'),
            'not_found_in_trash' => _x('No Location found in Trash', 'singlepro'),
            'parent_item_colon' => _x('Parent location:', 'location'),
            'menu_name' => _x('Geo Locater', 'location'),
        );
        $location_args = array(
            'labels' => $location_labels,
            'hierarchical' => false,
            'description' => 'Add your location',
            'supports' => array('title', 'thumbnail'),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => LOCATER_PLUGIN_URL . '/img/loc.png',
            'show_in_nav_menus' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => true,
            'can_export' => true,
            'rewrite' => true,
            'capability_type' => 'post'
        );

        register_post_type('location', $location_args);
    }

/**
 * location_meta_box
 *
 * Responsible for creating lables for custom metabox used by this plugin.
 *
 */
    public static function location_meta_box() {

        $location_meta_box = array(
            'id' => 'location_meta_box',
            'title' => __('Location informations', 'location'),
            'desc' => '',
            'pages' => array('location'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'label' => __('Address', 'location'),
                    'id' => 'address_loc',
                    'type' => 'text',
                    'class'=>'',
                    'desc' => __('Write address here.', 'location')
                ),
                array(
                    'label' => __('City', 'location'),
                    'id' => 'address_city',
                    'type' => 'text',
                    'class'=>'',
                    'desc' => __('Write City Name.', 'location'),
                ),
                array(
                    'label' => __('Country', 'location'),
                    'id' => 'address_country',
                    'type' => 'text',
                    'class'=>'',
                    'desc' => __('Write country Name.', 'location'),
                ),
                array(
                    'label' => __('&nbsp;', 'location'),
                    'id' => 'get_lat_long',
                    'type' => 'button',
                    'class'=> 'button button-primary button-large coordinates',
                    'desc' => __('Get Co-ordinates.', 'location'),
                ),
                array(
                    'label' => __('Longitude', 'location'),
                    'id' => 'address_long',
                    'type' => 'text',
                    'class'=>'',
                    'desc' => __('Write Longitude.', 'location'),
                ),
                array(
                    'label' => __('Latitude', 'location'),
                    'id' => 'address_lat',
                    'type' => 'text',
                    'class'=>'',
                    'desc' => __('Write Latitude.', 'location'),
                ),
            )
        );

        add_meta_box($location_meta_box['id'], $location_meta_box['title'], array('Locater', 'locater_build_metabox'), 'location', $location_meta_box['context'], $location_meta_box['priority'], $location_meta_box['fields']);
    }

/**
 * locater_build_metabox
 *
 * Responsible for generation html for custom metabox.
 *
 * Passed as argument to builtin function add_meta_box
 *
 * @param $object the current post object
 * @param $metabox_fields array containg metabox fields
 *
 */
    public static function locater_build_metabox($object, $metabox_fields) {
        wp_nonce_field(basename(__FILE__), $metabox_fields['id'] . '_nonce');
        ?>
        <div id="location_form" >

        <?php
        foreach ($metabox_fields['args'] as $field) {
            if ($field['type'] == 'text' || $field['type'] == 'button') {
                $entries = Locater_Api::get_locater_post_entries($object->ID);
                ?>
                    <?php if ($field['type'] == 'button') { ?>
                        <label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
                    <input id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" value="<?php echo $field['desc']; ?>" class="<?php echo $field['class']; ?>" >
 <br>
                        <?php }else{ ?>
                     <label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
                    <input id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" value="<?php echo esc_attr(@$entries[$field['id']]); ?>" class="<?php echo $field['class']; ?>" >

                       <?php  }  ?>
                    <br>

            <?php } else { ?>
                    <label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
                    <textarea id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" rows="<?php echo $field['rows']; ?>" ><?php echo esc_attr(@$entries[$field['id']]); ?></textarea>
                <?php }
            } ?>
            <input type="hidden" value="<?php echo get_option('locater_google_api_key') ?>" id="locater_google_api_key">
        </div>
        <?php
    }

/**
 * locater_save
 *
 * Responsible for saving metabox content(Location entries).
 *
 * @param int $post_id the current post ID
 * @param post $post the current post object
 * @param bool $update Whether this is an existing post being updated or not.
 *
 */
    static function locater_save($post_id, $post, $update) {
        if (!isset($_POST["location_meta_box_nonce"]) || !wp_verify_nonce($_POST["location_meta_box_nonce"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
            return $post_id;

        $slug = "location";
        if ($slug != $post->post_type)
            return $post_id;

        $address_loc = "";
        $address_city = "";
        $address_country = "";
        $address_long = "";
        $address_lat = "";




        if (isset($_POST["address_loc"])) {
            $address_loc = sanitize_text_field(wp_unslash($_POST["address_loc"]));
        }

        if (isset($_POST["address_city"])) {
            $address_city = sanitize_text_field(wp_unslash($_POST['address_city']));
        }

        if (isset($_POST["address_country"])) {
            $address_country = sanitize_text_field(wp_unslash($_POST["address_country"]));
        }

        if (isset($_POST["address_long"])) {
            $address_long = sanitize_text_field(wp_unslash($_POST["address_long"]));
        }

        if (isset($_POST["address_lat"])) {
            $address_lat = sanitize_text_field(wp_unslash($_POST["address_lat"]));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'locater_entries';

        if ($update && Locater_Api::get_locater_post_entries($post_id)) {
            $wpdb->update(
                    $table_name, array(
                'address_loc' => $address_loc,
                'address_city' => $address_city,
                'address_country' => $address_country,
                'address_long' => $address_long,
                'address_lat' => $address_lat,
                'post_id' => $post_id
                    ), array(
                'post_id' => $post_id
                    )
            );
        } else {
            $wpdb->insert(
                    $table_name, array(
                'address_loc' => $address_loc,
                'address_city' => $address_city,
                'address_country' => $address_country,
                'address_long' => $address_long,
                'address_lat' => $address_lat,
                'post_id' => $post_id
                    )
            );
        }
    }

/**
 * locater_settings
 *
 * Responsible for generate template for plugin's setting page.
 *
 */

    public static function locater_settings() {

        include LOCATER_PLUGIN_DIR . '/tpl/settings-template.php';
    }


/**
 * locater_shortcode
 *
 * Responsible for generate template for plugin's help & shortcode page.
 *
 */

    public static function locater_shortcode() {

        include LOCATER_PLUGIN_DIR . '/tpl/shortcode-template.php';
    }


/**
 * register_locater_settings
 *
 * Responsible for registering custome setting variable.
 *
 */
    public static function register_locater_settings() {
        register_setting('locater-settings-group', 'locater_google_api_key');
        register_setting('locater-settings-group', 'locater_map_zoom_size');
        register_setting('locater-settings-group', 'locater_map_radius');
        register_setting('locater-settings-group', 'locater_map_marker_img');
        register_setting('locater-settings-group', 'locater_sub_title_text');
    }

/**
 * locater_scripts
 *
 * Responsible for adding required js/css in admin section
 *
 */
    function locater_scripts() {
        if ( is_admin() ) {
            wp_enqueue_style("locater-css", LOCATER_PLUGIN_URL."/css/locater.css", false, "1.0", "all");
            wp_enqueue_script("locater-js", LOCATER_PLUGIN_URL."/js/locater.js", false, "1.0");
        }
    }
}