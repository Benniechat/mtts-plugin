<?php
namespace MttsLms\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DashboardSwitcherController {

    public static function init() {
        add_shortcode( 'mtts_dashboard_switcher', array( __CLASS__, 'render_switcher' ) );
        add_action( 'template_redirect', array( __CLASS__, 'check_access' ) );
    }

    public static function check_access() {
        if ( is_page( 'dashboard-switcher' ) && ! is_user_logged_in() ) {
            auth_redirect();
        }
    }

    public static function render_switcher() {
        $user = wp_get_current_user();
        $roles_info = array(
            'mtts_student'            => array( 'label' => 'Student Portal',    'url' => home_url('/student-dashboard'),      'icon' => '🎓' ),
            'mtts_lecturer'           => array( 'label' => 'Lecturer Portal',   'url' => home_url('/lecturer-dashboard'),     'icon' => '👨‍🏫' ),
            'mtts_accountant'         => array( 'label' => 'Financial Portal',  'url' => home_url('/accountant-dashboard'),   'icon' => '💰' ),
            'mtts_registrar'          => array( 'label' => 'Registrar Portal',  'url' => home_url('/registrar-dashboard'),    'icon' => '📜' ),
            'mtts_school_admin'       => array( 'label' => 'School Admin',      'url' => home_url('/school-admin-dashboard'), 'icon' => '⚙️' ),
            'mtts_campus_coordinator' => array( 'label' => 'Campus Office',     'url' => home_url('/campus-dashboard'),       'icon' => '🏫' ),
            'mtts_alumni'             => array( 'label' => 'Alumni Network',    'url' => home_url('/alumni-network'),         'icon' => '🌐' ),
            'administrator'           => array( 'label' => 'Super Admin',       'url' => admin_url(),                         'icon' => '👑' ),
        );

        ob_start();
        ?>
        <div class="mtts-switcher-container" style="max-width: 800px; margin: 50px auto; padding: 40px; text-align: center;">
            <h1 class="spiritual-gradient-text" style="font-size: 32px; margin-bottom: 10px;">Multiple Ministerial Paths</h1>
            <p style="color: #64748b; margin-bottom: 40px;">"For just as each of us has one body with many members, and these members do not all have the same function..."</p>
            
            <div class="mtts-switcher-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <?php foreach ( (array) $user->roles as $role ) : ?>
                    <?php if ( isset( $roles_info[ $role ] ) ) : $info = $roles_info[ $role ]; ?>
                        <a href="<?php echo esc_url( $info['url'] ); ?>" class="mtts-switcher-card" style="text-decoration: none; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); transition: 0.3s; border: 1px solid #f1f5f9; display: block;">
                            <div style="font-size: 48px; margin-bottom: 15px;"><?php echo $info['icon']; ?></div>
                            <h3 style="color: #1e293b; margin: 0;"><?php echo esc_html( $info['label'] ); ?></h3>
                            <span style="font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;"><?php echo esc_html( $role ); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <style>
            .mtts-switcher-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 35px rgba(124, 58, 237, 0.1);
                border-color: #7c3aed;
            }
        </style>
        <?php
        return ob_get_clean();
    }
}
