<?php
/*
Plugin Name: SMS Consent Management for WooCommerce
Plugin URI:  https://paladin-bs.com/plugins
Description: SMS Consent Management for WooCommerce
Author:      Peter MacIntyre
Version:     0.1.5
Author URI:  https://paladin-bs.com/about
Details URI: https://paladin-bs.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires Plugins: woocommerce

This plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
This plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
See License URI for full details.

Copyright (C) 2025 RingCentral
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* ============================== */
/* Set plugin Constant values */
/* ============================== */
if (!defined('WPWC_SMS_PLUGIN_VERSION')) {
	define('WPWC_SMS_PLUGIN_VERSION', "0.1.5");
}
if (!defined('WPWC_SMS_PLUGINDIR')) {
	define('WPWC_SMS_PLUGINDIR', plugin_dir_path(__FILE__));
}

/* ============================================= */
/* Add registration hook for plugin installation */
/* ============================================= */
register_activation_hook(__FILE__, 'wpwc_install');
function wpwc_install() {
	require_once(WPWC_SMS_PLUGINDIR . "includes/wpwc-install.php");
}

/* ========================================= */
/* Create default pages on plugin activation */
/* ========================================= */
register_activation_hook(__FILE__, 'wpwc_activation');
function wpwc_activation() {
	require_once(WPWC_SMS_PLUGINDIR . "includes/wpwc-activation.php");
}

/* ===================================== */
/* WooCommerce additional features start */
/* ===================================== */

/* ======================================= */
/* Show SMS consent choice per order view  */
/* on logged in account area in frontend   */
/* ======================================= */

add_action('woocommerce_after_order_details', 'wpwc_sms_consent_view_order_details');
function wpwc_sms_consent_view_order_details($order) {
	$wpwc_order_id = $order->get_id();
	$wpwc_sms_consent = get_post_meta($wpwc_order_id, 'wpwc_sms_consent', true);
	if ($wpwc_sms_consent == 1) {
		$wpwc_message = "You have opted into receiving SMS updates and promotional offers.";
	} else {
		$wpwc_message = "You have opted out of receiving SMS updates and promotional offers.";
	}
	echo "<p style='margin-top:15px;color:blue;'>" . esc_html($wpwc_message) . "</p>";
}

add_action('woocommerce_after_checkout_billing_form', 'wpwc_sms_consent_function');
function wpwc_sms_consent_function($checkout) {
	echo '<div id="your_custom_div">';
	woocommerce_form_field('wpwc_sms_consent', array(
		'type' => 'checkbox',
		'class' => array('your-custom-class form-row-wide'),
		'label' => 'Yes, please send me updates about my order and promotions via SMS.',
		//'placeholder'   => __('Enter something'),
		//'required'      => true,
	), $checkout->get_value('wpwc_sms_consent'));
	echo '</div>';
	echo "Data and message rates may apply. Message frequency varies. Reply HELP for help or STOP to opt-out. See privacy policy and terms of service for additional details.<br/><br/>";
}

add_action('woocommerce_checkout_update_order_meta', 'wpwc_save_custom_field_data');
function wpwc_save_custom_field_data($order_id) {
	// Save custom field to wp_postmeta table regardless of state
	// the order_id is stored in the post_id field
	// wpwc_sms_consent field name is stored in the meta_key field
	// the checkbox's value 0 or 1 is stored in the meta_value field

	// WooCommerce already verifies nonce before this runs.
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$wpwc_consent = !empty($_POST['wpwc_sms_consent']) ? 1 : 0;

	update_post_meta($order_id, 'wpwc_sms_consent', $wpwc_consent);
	update_post_meta($order_id, 'wpwc_sms_store_url', get_site_url());
}

?>
