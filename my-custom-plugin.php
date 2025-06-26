<?php
/**
 * Plugin Name: My Custom Plugin
 * Plugin URI: https://yourwebsite.com/my-plugin
 * Description: A plugin that registers a custom post type and adds admin features.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Siddhi Rajpurohit
 * Author URI: https://yourwebsite.com
 * Text Domain: my-custom-plugin
 * Domain Path: /languages
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) exit;

define('MY_PLUGIN_VERSION', '1.0.0');
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once MY_PLUGIN_PATH . 'includes/class-admin.php';
require_once MY_PLUGIN_PATH . 'includes/class-public.php';

function my_custom_plugin_init() {
    My_Custom_Plugin_Admin::get_instance();
    My_Custom_Plugin_Public::get_instance();
    error_log("Public class instantiated");
}
register_activation_hook(__FILE__, 'my_plugin_activate');
register_deactivation_hook(__FILE__, 'my_plugin_deactivate');

function my_plugin_activate() {
    global $wpdb;
    $table = $wpdb->prefix . 'my_plugin_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        text text NOT NULL,
        time datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    flush_rewrite_rules();
    add_option('my_plugin_version', MY_PLUGIN_VERSION);
}

function my_plugin_deactivate() {
    wp_clear_scheduled_hook('my_plugin_daily_event');
    flush_rewrite_rules();
}

add_action('plugins_loaded', 'my_custom_plugin_init');
