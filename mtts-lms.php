<?php
/**
 * Plugin Name: MTTS LMS Plugin
 * Plugin URI:  https://mttseminary.org
 * Description: A complete University-grade Learning Management System for Mountain-Top Theological Seminary.
 * Version:     1.2.1
 * Author:      BennieChat TechWealth Solutions
 * Author URI:  benniechatsystems@gmail.com
 * Text Domain: mtts-lms
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Plugin Constants
define( 'MTTS_LMS_VERSION', '1.3.0' );
define( 'MTTS_LMS_PATH', plugin_dir_path( __FILE__ ) );
define( 'MTTS_LMS_URL', plugin_dir_url( __FILE__ ) );
define( 'MTTS_LMS_FILE', __FILE__ );

// Autoloader - check if composer autoloader exists
if ( file_exists( MTTS_LMS_PATH . 'vendor/autoload.php' ) ) {
	require_once MTTS_LMS_PATH . 'vendor/autoload.php';
} else {
    // Fallback simple autoloader if composer dump-autoload hasn't been run yet
    spl_autoload_register(function ($class) {
        $prefix = 'MttsLms\\';
        $base_dir = MTTS_LMS_PATH . 'includes/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
}

/**
 * Main Plugin Class
 */
function mtts_lms_init() {
	if ( class_exists( 'MttsLms\\Core\\MttsLms' ) ) {
		\MttsLms\Core\MttsLms::get_instance();
	}
}
add_action( 'plugins_loaded', 'mtts_lms_init' );

/**
 * Activation Hook
 */
register_activation_hook( __FILE__, 'mtts_lms_activate' );
function mtts_lms_activate() {
    if ( class_exists( 'MttsLms\\Core\\Database\\Migration' ) ) {
        \MttsLms\Core\Database\Migration::run();
        \MttsLms\Core\Database\Migration::seed_default_forms();
    }
}

/**
 * Deactivation Hook
 */
register_deactivation_hook( __FILE__, 'mtts_lms_deactivate' );
function mtts_lms_deactivate() {
	// Flush rewrite rules or other cleanup if needed
    // Typically we don't drop tables on simple deactivation to preserve data
}
