<?php
defined( 'ABSPATH' ) or die( 'Direct access to file is not allowed' );
/**
 * Locater_Front
 *
 * Handle Front end functionality
 *
 */
require_once 'class.locater.api.php';
class Locater_Front {

    static $add_script;

/**
 * init
 *
 * Responsible for initialization os plugin, shortcode registration, enque script & style.
 *
 */
    public static function init() {
        add_shortcode('locater_map', array(__CLASS__, 'locater_map_func'));
        add_action('init', array(__CLASS__, 'register_script'));
        add_action('wp_footer', array(__CLASS__, 'print_script'));
        add_action('wp_head', array(__CLASS__, 'register_style'));
    }

/**
 * locater_map_func
 *
 * Actual short code function, Responsible for creation shortcode.
 *
 * @param $attr short code attribute if any.
 */
    public static function locater_map_func($atts) {
        self::$add_script = true;
        $map_zoom_size = Locater_Api::get_zoom_size();
        $map_radious = Locater_Api::get_map_radius();
        return '<div id="location_form">
               <p>'. get_option('locater_sub_title_text') .'</p>
               <label for="locater_search_filter">Search:</label>
                <input type="radio" checked="true" value="1" name="locater_search_filter" >Near By You &nbsp;&nbsp;
                <input type="radio"  value="2" name="locater_search_filter" >Specific Location

                <br>

 <input type="button" id="locater_btn" name="search" value="Search" style="float: right" />
  <div style="overflow: hidden; padding-right: .5em;">
    <input type="text" id="locater_input" name="term" placeholder="Type location here" style="width: 100%;" />
   </div>
   <div id="suggestion_box"></div>

                <div style="margin-top:30px; width: 100%; height:500px" class="locater_map" id="locater_map" >



                <img src="'.LOCATER_PLUGIN_URL.'/img/loader.gif" class="locater-ajax-loader">

                </div>
                                            <input type="hidden" value=""  id="currlat">
                <input type="hidden" value="" id="currlong">




        </div>

         <input type="hidden" value="' . get_option('locater_google_api_key') . '" id="locater_google_api_key">
         <input type="hidden" value="' . $map_zoom_size . '" id="locater_map_zoom_size">
         <input type="hidden" value="' . $map_marker_img . '" id="locater_map_marker_img">
         <input type="hidden" value="' . site_url() . '" id="locater_site_url">
        '
        ;
    }


/**
 * register_script
 *
 * Responsible for registering script.
 */
    public static function register_script() {
        wp_register_script('loc-google-script', 'http://maps.google.com/maps/api/js?key='.get_option('locater_google_api_key'));
        wp_register_script('loc-front-script', LOCATER_PLUGIN_URL . '/js/front_locater.js');
    }


/**
 * print_script
 *
 * Responsible for rendering script.
 */
    public static function print_script() {
        if (!self::$add_script)
            return;
        wp_print_scripts('loc-google-script');
        wp_print_scripts('loc-front-script');
    }


/**
 * register_style
 *
 * Responsible for registering style.
 *
 */
    public static function register_style() {
        wp_register_style('loc-front-style', LOCATER_PLUGIN_URL . '/css/locater.css');
        wp_enqueue_style( 'loc-front-style' );
    }

/**
 * locater_nearby
 *
 * Responsible for listing location from database base on specific location.
 * Called Via ajax.
 */
    public static function locater_nearby() {
        $args = array(
            'post_type' => 'location'
        );
        $lat_long = explode(',', sanitize_text_field($_POST['lat_long']));
        global $wpdb;

        if(isset($_POST['specific_loc']) && $_POST['specific_loc']==true){

            $query = "SELECT a.post_title,a.post_name,b.* FROM ". $wpdb->prefix.'posts'." as a LEFT JOIN ". $wpdb->prefix . 'locater_entries'." as b ON a.ID=b.post_id WHERE a.post_type =  'location' && a.post_status =  'publish' && b.address_lat =  '".$lat_long[0]."' && b.address_long =   '".$lat_long[1]."' ";

        }else{
            $query = "SELECT a.post_title,a.post_name,b.* FROM ". $wpdb->prefix.'posts'." as a LEFT JOIN ". $wpdb->prefix . 'locater_entries'." as b ON a.ID=b.post_id WHERE a.post_type =  'location' && a.post_status =  'publish' ";
        }

        $posts_array = $wpdb->get_results($query);
         foreach ($posts_array as $postmeta) {
            $postmeta = (array)$postmeta;
            $postResult['title'] = $postmeta['post_title'];
            $postResult['slug'] = $postmeta['post_name'];
            $postResult['address_loc'] = $postmeta['address_loc'];
            $postResult['address_country'] = $postmeta['address_country'];
            $postResult['latitude'] = floatval($postmeta['address_lat']);
            $postResult['address_city'] = $postmeta['address_city'];
            $postResult['longitude'] = floatval($postmeta['address_long']);
            $postData[] = $postResult;
            if (Locater_Api::distance($lat_long[0], $lat_long[1], $postResult['lat'], $postResult['long']) <= Locater_Api::get_map_radius()) {
                $postData[] = $postResult;
            }
        }
        echo json_encode($postData, JSON_PRETTY_PRINT);
        exit();
    }

/**
 * locater_suggestion
 *
 * Responsible for auto suggestion location from database base on search key.
 * Called Via ajax.
 *
 */
    public static function locater_suggestion() {

        $input_text = sanitize_text_field($_POST['locater_input_text']);
        $var = '%' . $input_text . '%';
        global $wpdb;
        $table_name = $wpdb->prefix . 'locater_entries';
        $entries = (array) $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE address_loc LIKE %s OR address_city LIKE %s OR address_country LIKE %s", $var, $var, $var));
        echo "<ul>";
        foreach ($entries as $entry) {

            $s = $entry->address_loc . ' ' . $entry->address_city . " " . $entry->address_country;
            ?>

            <li onclick='suggestion_fill("<?php echo $s; ?>")'><?php echo $s; ?></li>
            <?php
        }

        echo "<ul/>";
        exit();
    }

}