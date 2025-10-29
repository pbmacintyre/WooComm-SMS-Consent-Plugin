<?php
/*
Plugin Name: SMS Consent Management for WooCommerce
Plugin URI:  https://paladin-bs.com/plugins
Description: SMS Consent Management for WooCommerce
Author:      Peter MacIntyre
Version:     0.1.3
Author URI:  https://paladin-bs.com/about
Details URI: https://paladin-bs.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

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

/* ============================== */
/* Set plugin Constant values */
/* ============================== */
if (!defined('WOO_SMS_PLUGIN_VERSION')) {
	define('WOO_SMS_PLUGIN_VERSION', "0.1.3");
}
if (!defined('WOO_SMS_PLUGINDIR')) {
	define('WOO_SMS_PLUGINDIR', plugin_dir_path(__FILE__));
}

/* ============================== */
/* bring in PHP utility functions */
/* ============================== */
require_once("includes/ringcentral-php-functions.inc");

/* ============================================= */
/* Add registration hook for plugin installation */
/* ============================================= */
register_activation_hook(__FILE__, 'ringcentral_install');
function ringcentral_install() {
	require_once(WOO_SMS_PLUGINDIR . "includes/ringcentral-install.inc");
}

/* ========================================= */
/* Create default pages on plugin activation */
/* ========================================= */
register_activation_hook(__FILE__, 'ringcentral_activation');
function ringcentral_activation() {
	require_once(WOO_SMS_PLUGINDIR . "includes/ringcentral-activation.inc");
}

/* ===================================== */
/* WooCommerce additional features start */
/* ===================================== */

/* ======================================= */
/* Show SMS consent choice per order view  */
/* on logged in account area in frontend   */
/* ======================================= */

add_action('woocommerce_after_order_details', 'woo_sms_consent_view_order_details');
function woo_sms_consent_view_order_details($order) {
	$order_id = $order->get_id();
	$sms_consent = get_post_meta($order_id, 'woo_sms_consent', true);
	if ($sms_consent) {
		$message = "You have agreed to receive SMS messages regarding this order.";
	} else {
		$message = "You have chosen not to receive SMS messages regarding this order.";
	}
	echo_spaces($message, "", "blue", 1, 1);
}

add_action('woocommerce_after_checkout_billing_form', 'woo_sms_consent_function');
function woo_sms_consent_function($checkout) {
	echo '<div id="your_custom_div">';
	woocommerce_form_field('woo_sms_consent', array(
		'type' => 'checkbox',
		'class' => array('your-custom-class form-row-wide'),
		'label' => 'Grant SMS Consent for this order',
		//'placeholder'   => __('Enter something'),
		//'required'      => true,
	), $checkout->get_value('woo_sms_consent'));
	echo '</div>';
	$message = "Check the box above if you agree to receive SMS messages regarding this current order.";
	echo_spaces($message, "", "blue", 0, 0, 2);
}

add_action('woocommerce_checkout_update_order_meta', 'save_custom_field_data');

function save_custom_field_data($order_id) {
	// Save custom field to wp_postmeta table regardless of state
	// the order_id is stored in the post_id field
	// woo_sms_consent field name is stored in the meta_key field
	// the checkbox's value 0 or 1 is stored in the meta_value field
	if (isset($_POST['woo_sms_consent'])) {
		update_post_meta($order_id, 'woo_sms_consent', wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['woo_sms_consent'])), 'set_options'));
		update_post_meta($order_id, 'woo_sms_store_url', get_site_url());
	}
}
?>