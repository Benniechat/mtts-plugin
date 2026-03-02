<?php
/**
 * Admin Dashboard View
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$admin_url = admin_url( 'admin.php?page=' );
$menu_items = [
    [
        'title' => 'Programs',
        'desc'  => 'Manage academic programs, certificates, and durations.',
        'icon'  => 'welcome-learn-more',
        'slug'  => 'mtts-programs',
        'color' => '#7c3aed'
    ],
    [
        'title' => 'Sessions',
        'desc'  => 'Define and manage academic sessions and semesters.',
        'icon'  => 'calendar-alt',
        'slug'  => 'mtts-sessions',
        'color' => '#3b82f6'
    ],
    [
        'title' => 'Courses',
        'desc'  => 'Assign courses to programs and set credit units.',
        'icon'  => 'book-alt',
        'slug'  => 'mtts-courses',
        'color' => '#10b981'
    ],
    [
        'title' => 'Academic Calendar',
        'desc'  => 'Upload PDFs and manage important institutional events.',
        'icon'  => 'calendar',
        'slug'  => 'mtts-calendar',
        'color' => '#f59e0b'
    ],
    [
        'title' => 'Campus Centers',
        'desc'  => 'Configure locations and manage matriculation prefix codes.',
        'icon'  => 'location',
        'slug'  => 'mtts-campus-centers',
        'color' => '#ef4444'
    ],
    [
        'title' => 'Stakeholders',
        'desc'  => 'Register and manage staff, lecturers, and alumni.',
        'icon'  => 'groups',
        'slug'  => 'mtts-stakeholders',
        'color' => '#6366f1'
    ],
    [
        'title' => 'Form Builder',
        'desc'  => 'Create custom inquiry and application forms.',
        'icon'  => 'edit',
        'slug'  => 'mtts-form-builder',
        'color' => '#ec4899'
    ],
    [
        'title' => 'Shortcode Bank',
        'desc'  => 'Access all shortcodes for front-end integration.',
        'icon'  => 'editor-code',
        'slug'  => 'mtts-shortcode-bank',
        'color' => '#14b8a6'
    ],
    [
        'title' => 'Settings',
        'desc'  => 'Configure core plugin options and notifications.',
        'icon'  => 'admin-settings',
        'slug'  => 'mtts-settings',
        'color' => '#64748b'
    ],
];

?>

<div class="wrap mtts-admin-dashboard" style="background: #0f172a; color: #f8fafc; padding: 40px; border-radius: 16px; margin-top: 20px; font-family: 'Inter', system-ui, -apple-system, sans-serif;">
    
    <!-- Hero Header -->
    <div style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.2) 0%, rgba(30, 41, 59, 0) 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 40px; margin-bottom: 40px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: #7c3aed; filter: blur(120px); opacity: 0.2;"></div>
        
        <h1 style="color: #fff; font-size: 36px; font-weight: 800; margin: 0 0 15px 0; letter-spacing: -0.025em;">Welcome to MTTS LMS</h1>
        <p style="color: #94a3b8; font-size: 18px; line-height: 1.6; max-width: 800px; margin-bottom: 30px;">
            Your comprehensive Learning Management System for theological education. Manage programs, streamline student onboarding, and coordinate campus activities all from one central hub.
        </p>
        
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 15px 25px; display: flex; align-items: center; gap: 12px;">
                <span class="dashicons dashicons-shield-alt" style="color: #10b981;"></span>
                <span style="font-size: 14px; color: #cbd5e1;">Admin Role Active</span>
            </div>
            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 15px 25px; display: flex; align-items: center; gap: 12px;">
                <span class="dashicons dashicons-update" style="color: #3b82f6;"></span>
                <span style="font-size: 14px; color: #cbd5e1;">v1.2.0 (Latest)</span>
            </div>
        </div>
    </div>

    <!-- Quick Walkthrough -->
    <div style="margin-bottom: 50px;">
        <h2 style="color: #fff; font-size: 22px; font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
            <span class="dashicons dashicons-randomize" style="font-size: 20px; width: 20px; height: 20px;"></span>
            Quick Walkthrough
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            <div style="background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 25px;">
                <div style="width: 32px; height: 32px; background: rgba(124, 58, 237, 0.1); color: #7c3aed; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 15px;">1</div>
                <h3 style="color: #fff; margin-bottom: 10px;">Setup Academics</h3>
                <p style="color: #94a3b8; font-size: 14px; line-height: 1.5;">Define your Programs, Sessions, and Courses to build the academic foundation.</p>
            </div>
            <div style="background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 25px;">
                <div style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 15px;">2</div>
                <h3 style="color: #fff; margin-bottom: 10px;">Manage People</h3>
                <p style="color: #94a3b8; font-size: 14px; line-height: 1.5;">Register Staff, Lecturers, and Alumni. Assign roles and manage access levels.</p>
            </div>
            <div style="background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 25px;">
                <div style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 15px;">3</div>
                <h3 style="color: #fff; margin-bottom: 10px;">Go Live</h3>
                <p style="color: #94a3b8; font-size: 14px; line-height: 1.5;">Use the Shortcode Bank to embed registration forms and student portals on your site.</p>
            </div>
        </div>
    </div>

    <!-- Feature Hub -->
    <h2 style="color: #fff; font-size: 22px; font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <span class="dashicons dashicons-layout" style="font-size: 20px; width: 20px; height: 20px;"></span>
        Academic Feature Hub
    </h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php foreach ( $menu_items as $item ) : ?>
            <a href="<?php echo esc_url( $admin_url . $item['slug'] ); ?>" style="text-decoration: none; display: block; group;">
                <div class="mtts-dashboard-card" style="background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 25px; transition: all 0.3s ease; height: 100%; box-sizing: border-box;">
                    <div style="background: <?php echo $item['color']; ?>15; border-radius: 12px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; transition: all 0.3s ease;">
                        <span class="dashicons dashicons-<?php echo $item['icon']; ?>" style="color: <?php echo $item['color']; ?>; font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <h3 style="color: #fff; margin: 0 0 10px 0; font-size: 18px; font-weight: 600;"><?php echo esc_html( $item['title'] ); ?></h3>
                    <p style="color: #94a3b8; font-size: 14px; line-height: 1.5; margin: 0;"><?php echo esc_html( $item['desc'] ); ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
.mtts-dashboard-card:hover {
    background: rgba(30, 41, 59, 0.8) !important;
    border-color: rgba(255,255,255,0.1) !important;
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
}
.mtts-dashboard-card:hover div {
    transform: scale(1.1);
}
</style>
