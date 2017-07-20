<?php
defined( 'ABSPATH' ) or die( 'Direct access to file is not allowed' );
/*
Plugin Name: Geo Locater
Description: This is a very simple address locator map added to your site and lets your visitors to start finding you quickly!.
Author: Nitin Maurya
Version: 1.2
Author URI:http://www.kelltontech.com/
Plugin URI:https://wordpress.org/plugins/geo-locater/
*/

require_once 'classes/class.locater.php';
require_once 'classes/class.locater.api.php';
require_once 'classes/class.locater.front.php';

/**
 *
 * Defined plugin constant.
 *
 */
if (!defined('LOCATER_MAP_ZOOM_SIZE'))
    define('LOCATER_MAP_ZOOM_SIZE', 10);

if (!defined('LOCATER_MAP_RADIUS'))
    define('LOCATER_MAP_RADIUS', 50);

if (!defined('LOCATER_THEME_DIR'))
    define('LOCATER_THEME_DIR', get_template_directory());

if (!defined('LOCATER_PLUGIN_NAME'))
    define('LOCATER_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('LOCATER_PLUGIN_DIR'))
    define('LOCATER_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . LOCATER_PLUGIN_NAME);

if (!defined('LOCATER_PLUGIN_URL'))
    define('LOCATER_PLUGIN_URL', WP_PLUGIN_URL . '/' . LOCATER_PLUGIN_NAME);

if (!defined('LOCATER_VERSION_KEY'))
    define('LOCATER_VERSION_KEY', 'locater_version');

if (!defined('LOCATER_VERSION_NUM'))
    define('LOCATER_VERSION_NUM', '1.0');


if (!defined('LOCATER_MAP_MARKER'))
    define('LOCATER_MAP_MARKER', LOCATER_PLUGIN_URL.'/img/red-dot.png');

/**
 *
 * Plugin activation hook.
 * Responsible for creating/modifying database table used bythis plugin
 *
 */
function locater_activation() {

    global $wpdb;
    $table_name = $wpdb->prefix . "locater_entries";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    address_loc varchar(100),
    address_city varchar(100),
    address_country varchar(100),
    address_long varchar(100),
    address_lat varchar(100),
    post_id int(11) NOT NULL
   ) $charset_collate;";
    $wpdb->show_errors();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    if (get_option(LOCATER_VERSION_KEY) != LOCATER_VERSION_NUM) {
        update_option(LOCATER_VERSION_KEY, LOCATER_VERSION_NUM);
    }
    $wpdb->show_errors();
}
register_activation_hook(__FILE__, 'locater_activation');


/**
 * Bind various plugin action with worpdpress hook.
 */
add_action('init', array('Locater','register_post_type'), 0);
add_action("admin_head", array('Locater','locater_scripts'), 11);
//add_action("admin_head", array('Locater','locater_styles'), 11);
add_action("add_meta_boxes", array('Locater','location_meta_box'));
add_action("save_post", array('Locater',"locater_save"), 10, 3);

/**
 * Initialize the plugin.
 */
Locater_Front::init();

/**
 * Register action call via ajax request.
 */
add_action('wp_ajax_locater_nearby',array('Locater_Front','locater_nearby'));
add_action('wp_ajax_nopriv_locater_nearby',array('Locater_Front','locater_nearby'));
add_action('wp_ajax_locater_suggestion',array('Locater_Front','locater_suggestion'));
add_action('wp_ajax_nopriv_locater_suggestion',array('Locater_Front','locater_suggestion'));

/**
 * Hook plugin menu into admin menu.
 */
add_action('admin_menu', array('Locater','locater_menu'));