<?php
/**
 * Shortcode instruction.
 *
 */



if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

$title = __('Locater Shortcode');
$instructions  = __('Follow these instructions use this plugin');


/* translators: date and time format for exact current time, mainly about timezones, see http://php.net/date */
$timezone_format = _x('Y-m-d H:i:s', 'timezone date format');

?>

<div class="wrap">
<h2><?php echo esc_html( $title ); ?></h2>

<p><?php echo esc_html( $instructions ); ?></p>
<p><?php echo esc_html( '1. Create various location form "Locations->Add New Location" ' ); ?></p>
<p><?php echo esc_html( '2. Now Use following sortcode on any page or post where you want location map serach functionality.' ); ?></p>
<p style="font-size: 14px; padding:10px" ><em>[locater_map]</em></p>
</div>

