<?php
/**
 * General settings locater plugin.
 *
 */

if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

$title = __('Locater Settings');

/* translators: date and time format for exact current time, mainly about timezones, see http://php.net/date */
$timezone_format = _x('Y-m-d H:i:s', 'timezone date format');

/**
 * Display JavaScript on the page.
 *
 * @since 3.5.0
 */
function gl_options_general_add_js() {
?>
<script type="text/javascript">
	jQuery(document).ready(function($){
	});
</script>
<?php
}
add_action('admin_head', 'gl_options_general_add_js');

$options_help = '<p>' . __('The fields on this screen determine some of the basics of your site setup.') . '</p>' .
	'<p>' . __('Most themes display the site title at the top of every page, in the title bar of the browser, and as the identifying name for syndicated feeds. The tagline is also displayed by many themes.') . '</p>';



?>

<div class="wrap">
<h2><?php echo esc_html( $title ); ?></h2>

<form method="post" action="options.php" novalidate="novalidate">
<?php settings_fields( 'locater-settings-group' ); ?>
<?php do_settings_sections( 'locater-settings-group' ); ?>
<p class="description" id="locater_map_zoom_size-description"><?php _e( 'Manage your locater settings here.' ) ?></p>
<table class="form-table">
<tr>
<th scope="row"><label for="locater_sub_title_text"><?php _e('Sub Title Text') ?></label></th>
<td><input name="locater_sub_title_text" type="text" id="locater_sub_title_text" value="<?php form_option('locater_sub_title_text'); ?>" class="regular-text" />
<p class="description" id="locater_sub_title_text-description"><?php _e( 'This text will be displayed on top of the Map.' ) ?></p>
</td>
<tr>
<th scope="row"><label for="locater_google_api_key"><?php _e('GeoCoding Google API Key') ?></label></th>
<td><input name="locater_google_api_key" type="text" id="locater_google_api_key" value="<?php form_option('locater_google_api_key'); ?>" class="regular-text" />
<p class="description" id="locater_google_api_key-description"><?php _e( 'Optional, But you should use a geo coding api key to track each geo coding request. It can be useful if you want to extends geo coding request limits. For more detail <a href="https://developers.google.com/maps/documentation/geocoding/" target="_blank">click here</a>  ' ) ?></p>
</td>

</tr>
<tr>
<th scope="row"><label for="locater_map_zoom_size"><?php _e('Map Zoom Size') ?></label></th>
<td><input name="locater_map_zoom_size" type="text" placeholder="10" id="blogdescription" aria-describedby="locater_map_zoom_size-description" value="<?php form_option('locater_map_zoom_size'); ?>" class="regular-text" />
<p class="description" id="locater_map_zoom_size-description"><?php _e( 'Optional, If not provided, standard size(10)will be used.' ) ?></p></td>
</tr>

<tr>
<th scope="row"><label for="locater_map_radius"><?php _e('Location Radius') ?></label></th>
<td><input name="locater_map_radius" type="text" id="blogdescription" placeholder="50" aria-describedby="locater_map_radius-description" value="<?php form_option('locater_map_radius'); ?>" class="regular-text" />*in Kilometers
<p class="description" id="locater_map_radius-description"><?php _e( 'Optional, default is 50km ' ) ?></p></td>
</tr>


<tr>
<th scope="row"><label for="locater_map_marker_img"><?php _e('Marker Image URL') ?></label></th>
<td><input name="locater_map_marker_img" type="text" id="blogdescription" placeholder="Example: http://maps.google.com/mapfiles/ms/icons/red-dot.png" aria-describedby="locater_map_marker_img-description" value="<?php form_option('locater_map_marker_img'); ?>" class="regular-text" />
<p class="description" id="locater_map_marker_img-description"><?php _e( 'Optional,Please provide a valid marker image url, If not provided, default will be used ' ) ?></p></td>
</tr>



</table>


<?php submit_button(); ?>
</form>

</div>

