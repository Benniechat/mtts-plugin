<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MttsLms {

	/**
	 * Single instance of the class
	 *
	 * @var MttsLms
	 */
	protected static $instance = null;

	/**
	 * Returns the single instance of the class.
	 *
	 * @return MttsLms
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
        // Include other core files if not autoloaded or specific functional files
        // e.g., require_once MTTS_LMS_PATH . 'includes/functions.php';
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
        
        \MttsLms\Core\Roles::init();
        \MttsLms\Core\NotificationManager::init();
        \MttsLms\Controllers\AuthController::init();
        \MttsLms\Controllers\DashboardSwitcherController::init();
        \MttsLms\Core\JwtAuth::init();
        \MttsLms\Core\Api\RestController::init();
        \MttsLms\Controllers\Admin\AcademicController::init();
        \MttsLms\Controllers\Admin\PeopleController::init();
        \MttsLms\Controllers\Admin\RoleController::init();
        \MttsLms\Controllers\AdmissionController::init();
        
        add_filter( 'body_class', array( $this, 'add_body_classes' ) );
        \MttsLms\Controllers\Admin\AdmissionAdminController::init();
        \MttsLms\Controllers\Admin\FormController::init();
        \MttsLms\Controllers\Admin\AlumniAdminController::init();
        \MttsLms\Controllers\Student\StudentDashboardController::init();
        \MttsLms\Controllers\Lecturer\LecturerDashboardController::init();
        \MttsLms\Controllers\Student\WalletController::init();
        \MttsLms\Controllers\Admin\BulkAdmissionController::init();
        \MttsLms\Controllers\Admin\ReportsController::init();
        \MttsLms\Controllers\Admin\SettingsController::init();
        \MttsLms\Controllers\Admin\MockDataController::init();
        \MttsLms\Controllers\ReceiptController::init();
        \MttsLms\Controllers\PdfController::init();
        \MttsLms\Controllers\FrontendController::init();
        \MttsLms\Controllers\AlumniController::init();
        \MttsLms\Controllers\FrontendAdminController::init();
        \MttsLms\Controllers\ZoomController::init();
        \MttsLms\Controllers\WebhookController::init();
        \MttsLms\Core\Cron::init();
        \MttsLms\Core\FormShortcode::init();
        \MttsLms\Core\ShortcodeBank::init();
        \MttsLms\Controllers\PortfolioController::init();
        \MttsLms\Core\Translator::init();
    }

	/**
	 * Init WordPress when loaded.
	 */
	public function init() {
		// Register custom post types, taxonomies, endpoints here
        // Route::init(); // Initialize routing if needed here
        $this->load_textdomain();
	}

    /**
	 * Load Localisation files.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'mtts-lms', false, dirname( plugin_basename( MTTS_LMS_FILE ) ) . '/languages/' );
	}

    public function admin_scripts() {
        // wp_enqueue_style( 'mtts-lms-admin', MTTS_LMS_URL . 'assets/css/admin.css', array(), MTTS_LMS_VERSION );
        // wp_enqueue_script( 'mtts-lms-admin', MTTS_LMS_URL . 'assets/js/admin.js', array( 'jquery' ), MTTS_LMS_VERSION, true );
    }

    public function frontend_scripts() {
        wp_enqueue_style( 'mtts-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null );
        wp_enqueue_style( 'mtts-lms-css', MTTS_LMS_URL . 'assets/css/mtts-lms.css', array('dashicons'), MTTS_LMS_VERSION );
        wp_enqueue_script( 'mtts-lms-js', MTTS_LMS_URL . 'assets/js/mtts-lms.js', array('jquery'), MTTS_LMS_VERSION, true );
        
        // Dynamic Header Height Calculation Script
        $dynamic_header_js = "
            document.addEventListener('DOMContentLoaded', function() {
                function updateMttsHeaderHeight() {
                    // Try to find common sticky/fixed header selectors
                const headerSelectors = ['header', '.site-header', '#masthead', '.td-header-wrap', '.main-header', '.header-wrapper', '.navbar-fixed-top', '#header', '.elementor-location-header', '.elementor-header', '.sticky-header'];
                    let themeHeader = null;
                    
                    for (let selector of headerSelectors) {
                        const el = document.querySelector(selector);
                        // Check if it's fixed or sticky AND actually has height
                        if (el && el.offsetHeight > 0) {
                            const style = window.getComputedStyle(el);
                            if (style.position === 'fixed' || style.position === 'sticky') {
                                themeHeader = el;
                                break;
                            } else if (selector === 'header' || selector === '.site-header' || selector === '#masthead') {
                                // Fallback: even if not strictly fixed, often the main header pushes content down
                                themeHeader = el;
                                break;
                            }
                        }
                    }

                    if (themeHeader) {
                        const height = themeHeader.offsetHeight;
                        document.documentElement.style.setProperty('--mtts-theme-header-height', height + 'px');
                    } else {
                        document.documentElement.style.setProperty('--mtts-theme-header-height', '0px');
                    }
                }

                // Run initially
                updateMttsHeaderHeight();
                
                // Run multiple times as images/logos load
                window.addEventListener('load', updateMttsHeaderHeight);
                window.addEventListener('resize', updateMttsHeaderHeight);
                
                // Aggressive checking for first 3 seconds
                setTimeout(updateMttsHeaderHeight, 500);
                setTimeout(updateMttsHeaderHeight, 1500);
                setTimeout(updateMttsHeaderHeight, 3000);
            });
        ";
        wp_add_inline_script( 'mtts-lms-js', $dynamic_header_js );
    }

    /**
     * Add custom body classes to LMS pages
     */
    public function add_body_classes( $classes ) {
        $lms_pages = array(
            'student-dashboard',
            'lecturer-dashboard',
            'school-admin-dashboard',
            'registrar-dashboard',
            'accountant-dashboard',
            'campus-dashboard',
            'alumni-network'
        );

        foreach ( $lms_pages as $slug ) {
            if ( is_page( $slug ) ) {
                $classes[] = 'mtts-lms-page';
                break;
            }
        }

        return $classes;
    }

}
