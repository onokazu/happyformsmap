<?php
/**
 * Plugin Name: Happyforms Map
 * Description: Map field for Happyforms.
 * Version:     1.0.0
 * Text Domain: happyformsmap
 * Domain Path: /languages
 */

if ( ! defined( 'HAPPYFORMS_VERSION' ) && ! defined( 'HAPPYFORMS_UPGRADE_VERSION' ) ) return;

define( 'HAPPYFORMSMAP_VERSION', '1.0.0' );
define( 'HAPPYFORMSMAP_PATH', __DIR__ );
define( 'HAPPYFORMSMAP_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'HAPPYFORMSMAP_LEAFLET_VERSION', '1.9.4' );

add_action( 'plugins_loaded', function() {
    // Helpers
    require_once HAPPYFORMSMAP_PATH . '/inc/helper-misc.php';
    // Map part
    require_once HAPPYFORMSMAP_PATH . '/inc/class-part.php';
    happyforms_get_part_library()->register_part( 'HappyFormsMap_Part', 26 );
    // Init admin
    if ( defined( 'HAPPYFORMS_UPGRADE_VERSION' ) && is_admin() ) {
        require_once HAPPYFORMSMAP_PATH . '/inc/class-admin.php';
        HappyFormsMap_Admin::init();
    }
}, 11 );