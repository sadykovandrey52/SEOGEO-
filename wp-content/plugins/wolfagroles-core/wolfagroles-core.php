<?php
/**
 * Plugin Name: Wolfagroles Core
 * Description: Контентные модели, факты, формы, SEO, schema и начальный контент сайта ООО «Вольфагролес».
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: ООО «Вольфагролес»
 * Text Domain: wolfagroles-core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WG_CORE_VERSION', '1.0.0' );
define( 'WG_CORE_FILE', __FILE__ );
define( 'WG_CORE_DIR', plugin_dir_path( __FILE__ ) );

require_once WG_CORE_DIR . 'includes/settings.php';
require_once WG_CORE_DIR . 'includes/content-model.php';
require_once WG_CORE_DIR . 'includes/seed-content.php';
require_once WG_CORE_DIR . 'includes/rendering.php';
require_once WG_CORE_DIR . 'includes/forms.php';
require_once WG_CORE_DIR . 'includes/seo-schema.php';

function wg_core_activate() {
    wg_register_content_model();
    wg_seed_content();
    wg_ensure_private_upload_directory();
    if ( ! wp_next_scheduled( 'wg_cleanup_lead_files' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'wg_cleanup_lead_files' );
    }
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wg_core_activate' );

function wg_core_deactivate() {
    $timestamp = wp_next_scheduled( 'wg_cleanup_lead_files' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'wg_cleanup_lead_files' );
    }
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wg_core_deactivate' );

add_action(
    'plugins_loaded',
    function () {
        load_plugin_textdomain( 'wolfagroles-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
);
