<?php
defined( 'ABSPATH' ) or die( 'Direct access to file is not allowed' );
/**
 * Locater API
 *
 * Privide function used in othe classes.
 *
 */
class Locater_Api {

/**
 * get_api_key
 *
 * Return google geo coding api key from database if saved already.
 *
 */
    public static function get_api_key() {
        $key = get_option('locater_google_api_key');

        if (trim($key) != '') {
            return '';
        }
    }

/**
 * get_api_key
 *
 * Return map zoom size from database if saved already othewise defult.
 *
 */
    public static function get_zoom_size() {
        $key = get_option('locater_map_zoom_size');
        $key = intval(trim($key));
        if ($key) {
            return $key;
        } else {
            return LOCATER_MAP_ZOOM_SIZE;
        }
    }

/**
 * get_map_radius
 *
 * Return map radius from database if saved already othewise defult.
 */
    public static function get_map_radius() {
        $key = get_option('locater_map_radius');
        $key = intval(trim($key));
        if ($key) {
            return $key;
        } else {
            return LOCATER_MAP_RADIUS;
        }
    }


/**
 * get_map_marker_img
 *
 * Return map marker image from database if saved already othewise defult.
 */

    public static function get_map_marker_img() {
        $key = get_option('locater_map_marker_img');
        if (filter_var(trim($key), FILTER_VALIDATE_URL) === false) {
            return LOCATER_MAP_MARKER;
        } else {
            return $key;
        }
    }


/**
 * get_map_marker_img
 *
 * Calculate distance between two geo coordinate.
 *
 * @param $lat1 latitude of first coordinate
 * @param $lon1 longitude of first coordinate
 * @param $lat2 latitude of second coordinate
 * @param $lon2 longitude of second coordinate
 *
 * @return distance in Kilometers
 */

    public static function distance($lat1, $lon1, $lat2, $lon2) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return ($miles * 1.609344);
    }


/**
 * get_map_marker_img
 *
 * Calculate distance between two geo coordinate.
 *
 * @param $post_id int Post ID
 * @return array containg attribute of a location corresponding to a post
 */

    public static function get_locater_post_entries($post_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'locater_entries';
        $entries = $wpdb->get_row("SELECT * FROM $table_name WHERE post_id=$post_id");

        $numrows = count($entries);
        if ($numrows < 1) {
            return false;
        } else {
            return (array) $entries;
        }
    }

}