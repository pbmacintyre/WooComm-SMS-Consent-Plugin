<?php
/*
Plugin Name: WooCommerce SMS Consent Management
Plugin URI:  https://paladin-bs.com/plugins
Description: WooCommerce SMS Consent Management Plugin
Author:      Peter MacIntyre
Version:     0.1.0
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

Copyright (C) 2024-2025 Paladin Business Solutions
*/

/* ============================== */
/* Set plugin Constant values */
/* ============================== */
if (!defined('WOO_SMS_PLUGIN_VERSION')) {
    define('WOO_SMS_PLUGIN_VERSION', "0.1.0");
}
if (!defined('WOO_SMS_PLUGINDIR')) {
    define('WOO_SMS_PLUGINDIR', plugin_dir_path(__FILE__));
}
if (!defined('WOO_SMS_PLUGINURL')) {
    define('WOO_SMS_PLUGINURL', plugin_dir_url(__FILE__));
    //  http path returned
}
if (!defined('WOO_SMS_PLUGIN_INCLUDES')) {
    define('WOO_SMS_PLUGIN_INCLUDES', plugin_dir_path(__FILE__) . "includes/");
}
if (!defined('WOO_SMS_PLUGIN_FILENAME')) {
    define('WOO_SMS_PLUGIN_FILENAME', plugin_basename(dirname(__FILE__) . '/ringcentral.php'));
}

/* ============================== */
/* bring in PHP utility functions */
/* ============================== */
require_once("includes/ringcentral-php-functions.inc");

/* ====================================== */
/* bring in generic RingCentral functions */
/* ====================================== */
require_once("includes/ringcentral-functions.inc");

/* ================================== */
/* bring in RingCentral 2FA functions */
/* ================================== */
require_once("includes/ringcentral-2fa-functions.inc");

/* ================================= */
/* set RingCentral supporting cast  */
/* ================================= */
function ringcentral_js_add_script() {
    $js_path = WOO_SMS_PLUGINURL . 'js/ringcentral-scripts.js';
    wp_enqueue_script('ringcentral-js', $js_path);
}

add_action('init', 'ringcentral_js_add_script');

function ringcentral_js_add_admin_script() {
    $js_path = WOO_SMS_PLUGINURL . 'js/ringcentral-admin-scripts.js';
    wp_enqueue_script('ringcentral-admin-js', $js_path);
}

add_action('admin_enqueue_scripts', 'ringcentral_js_add_admin_script');

function ringcentral_load_custom_admin_css() {
    wp_register_style('ringcentral_custom_admin_css',
            WOO_SMS_PLUGINURL . 'css/ringcentral-custom.css',
            false, '1.0.0');
    wp_enqueue_style('ringcentral_custom_admin_css');
}

add_action('admin_print_styles', 'ringcentral_load_custom_admin_css');

/* ============================================= */
/* Add registration hook for plugin installation */
/* ============================================= */
function ringcentral_install() {
    require_once(WOO_SMS_PLUGINDIR . "includes/ringcentral-install.inc");
}

/* ========================================= */
/* Create default pages on plugin activation */
/* ========================================= */
function ringcentral_install_default_pages() {
    require_once(WOO_SMS_PLUGINDIR . "includes/ringcentral-activation.inc");
}

register_activation_hook(__FILE__, 'ringcentral_install');
register_activation_hook(__FILE__, 'ringcentral_install_default_pages');

/* ===================================== */
/* WooCommerce additional features start */
/* ===================================== */

//add_action('woocommerce_after_order_notes', 'woo_sms_consent_function');
add_action('woocommerce_after_checkout_billing_form', 'woo_sms_consent_function');

function woo_sms_consent_function($checkout) {
	echo '<div id="your_custom_div">';
	woocommerce_form_field('woo_sms_consent', array(
		'type'          => 'checkbox',
		'class'         => array('your-custom-class form-row-wide'),
		'label'         => __('Grant SMS Consent'),
		//'placeholder'   => __('Enter something'),
		//'required'      => true,
	), $checkout->get_value('woo_sms_consent'));
	echo '</div>';
	$message = "Check the box above if you agree to receive SMS messages regarding your orders from this shop." ;
	echo_spaces( $message, "","blue", 0, 2);
}

add_action('woocommerce_checkout_update_order_meta', 'save_custom_field_data');

function save_custom_field_data($order_id) {
	//Saved to wp_postmeta table
	if (!empty($_POST['woo_sms_consent'])) {
		// the order_id is stored in the post_id field
		// woo_sms_consent field name is stored in the meta_key field
		// the checkbox's value 0 or 1 is stored in the meta_value field
		update_post_meta($order_id, 'woo_sms_consent', sanitize_text_field($_POST['woo_sms_consent']));
	}
}


// Display checkbox on registration form
add_action( 'woocommerce_register_form', 'sms_consent_customer_reg' );
function sms_consent_customer_reg() {
	?>
	<p class="form-row form-row-wide">
		<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
			<input type="checkbox" name="sms_customer_consent" id="sms_customer_consent" value="0" class="woocommerce-form__input woocommerce-form__input-checkbox" />
			<span>I agree to receive SMS notifications from this store.</span>
		</label>
	</p>
	<?php
}

// Save registration checkbox value in user meta
add_action( 'woocommerce_created_customer', 'sms_consent_customer_save' );
function sms_consent_customer_save( $customer_id ) {
	$sms_consent = isset( $_POST['sms_customer_consent'] ) ? 'yes' : 'no';
	update_user_meta( $customer_id, 'sms_customer_consent', $sms_consent );
}

// =====================================
// =====================================
// =====================================

add_action( 'woocommerce_edit_account_form', 'sms_consent_customer_show' );
function sms_consent_customer_show() {
	$user_id = get_current_user_id();
	$sms_consent = get_user_meta( $user_id, 'sms_customer_consent', true );
	?>
	<p class="form-row form-row-wide">
		<label>
			<input type="checkbox" name="sms_customer_consent" <?php checked( $sms_consent, 'yes' ); ?> />
			<span>I agree to receive SMS notifications.</span>
		</label>
	</p>
	<?php
}

add_action( 'woocommerce_save_account_details', 'my_custom_account_checkbox_save' );
function my_custom_account_checkbox_save( $user_id ) {
	update_user_meta( $user_id, 'sms_customer_consent', isset( $_POST['sms_customer_consent'] ) ? 'yes' : 'no' );
}

// =====================================
// =====================================
// =====================================


// Add column to Users → All Users
add_filter( 'manage_users_columns', function( $columns ) {
	$columns['sms_customer_consent'] = __( 'SMS Consent', 'woocommerce' );
	return $columns;
});

add_filter( 'manage_users_custom_column', function( $value, $column_name, $user_id ) {
	if ( $column_name === 'sms_customer_consent' ) {
		$consent = get_user_meta( $user_id, 'sms_customer_consent', true );
		return $consent === 'yes' ? '✅ Yes' : '❌ No';
	}
	return $value;
}, 10, 3 );
?>