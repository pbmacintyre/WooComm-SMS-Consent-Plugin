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

if (!defined('WOO_SMS_LOGO')) {
    define('WOO_SMS_LOGO', WOO_SMS_PLUGINURL . 'images/rc_logo_60_60.jpg');
}
if (!defined('RINGCENTRAL_FULL_LOGO')) {
    define('WOO_SMS_FULL_LOGO', WOO_SMS_PLUGINURL . 'images/rc-logo.png');
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

?>