<?php
/**
 * Shared Sidebar for all Frontend Admin Portals
 * Dynamically shows nav links based on current user role.
 */
$user  = wp_get_current_user();
$roles = (array) $user->roles;
$view  = isset($_GET['view']) ? sanitize_key($_GET['view']) : 'overview';

// Determine which dashboard is currently being viewed
$current_page    = get_queried_object();
$current_slug    = $current_page ? $current_page->post_name : '';

// Define nav links per role
$nav_links = array();
$portal_url = get_permalink();

if ( in_array('mtts_school_admin', $roles) || current_user_can('manage_options') ) {
    $nav_links['school-admin'] = [
        'url'   => home_url('/school-admin-dashboard'),
        'label' => 'School Admin',
        'icon'  => 'dashicons-admin-settings',
        'views' => [
            'overview'    => '📊 Overview',
            'students'    => '🎓 Students',
            'lecturers'   => '👨‍🏫 Lecturers',
            'programs'    => '📚 Programs',
            'applications'=> '📋 Applications',
        ]
    ];
}
if ( in_array('mtts_registrar', $roles) || current_user_can('manage_options') ) {
    $nav_links['registrar'] = [
        'url'   => home_url('/registrar-dashboard'),
        'label' => 'Registrar',
        'icon'  => 'dashicons-id-alt',
        'views' => [
            'overview'    => '📊 Overview',
            'applications'=> '📋 Applications',
            'students'    => '🎓 Students',
            'reports'     => '📈 Reports',
        ]
    ];
}
if ( in_array('mtts_accountant', $roles) || current_user_can('manage_options') ) {
    $nav_links['accountant'] = [
        'url'   => home_url('/accountant-dashboard'),
        'label' => 'Accountant',
        'icon'  => 'dashicons-money-alt',
        'views' => [
            'overview'     => '📊 Overview',
            'payments'     => '💳 Payments',
            'transactions' => '🔄 Transactions',
            'reports'      => '📈 Reports',
        ]
    ];
}
if ( in_array('mtts_campus_coordinator', $roles) || current_user_can('manage_options') ) {
    $nav_links['campus'] = [
        'url'   => home_url('/campus-dashboard'),
        'label' => 'Campus',
        'icon'  => 'dashicons-location',
        'views' => [
            'overview'  => '📊 Overview',
            'students'  => '🎓 My Students',
            'attendance'=> '✅ Attendance',
            'reports'   => '📈 Reports',
        ]
    ];
}
?>

<button class="mtts-mobile-toggle"><span class="dashicons dashicons-menu"></span> Menu</button>
<div class="mtts-sidebar">
    <button class="mtts-sidebar-close">&times;</button>

    <div class="mtts-student-info">
        <div class="mtts-avatar">
            <img src="<?php echo get_avatar_url( $user->ID ); ?>" alt="Profile">
        </div>
        <h4><?php echo esc_html( $user->display_name ); ?></h4>
        <small style="color:#a78bfa;"><?php echo esc_html( implode(', ', array_map('ucwords', str_replace('mtts_', '', $roles)))); ?></small>
    </div>

    <nav class="mtts-nav">
        <ul>
            <?php foreach ($nav_links as $key => $portal): ?>
                <?php
                $is_active_portal = (strpos($current_slug, $key) !== false);
                $active_views = $is_active_portal ? $portal['views'] : [];
                ?>
                <li>
                    <a href="<?php echo esc_url($portal['url']); ?>" style="<?php echo $is_active_portal ? 'color:#a78bfa;font-weight:700;' : ''; ?>">
                        <span class="dashicons <?php echo $portal['icon']; ?>"></span> <?php echo $portal['label']; ?>
                    </a>
                    <?php if ($is_active_portal && !empty($active_views)): ?>
                    <ul style="padding-left:15px; margin-top:5px;">
                        <?php foreach ($active_views as $v => $label): ?>
                        <li>
                            <a href="?view=<?php echo $v; ?>" class="<?php echo $view === $v ? 'active' : ''; ?>" style="font-size:0.9em;">
                                <?php echo $label; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>

            <li style="margin-top:20px; border-top:1px solid rgba(255,255,255,0.1); padding-top:15px;">
                <a href="<?php echo esc_url(home_url('/alumni-network')); ?>" style="color:#a78bfa; font-weight:600;">
                    <span class="dashicons dashicons-groups"></span> Alumni Network
                </a>
            </li>
            <li>
                <a href="<?php echo wp_logout_url(home_url()); ?>">
                    <span class="dashicons dashicons-exit"></span> Logout
                </a>
            </li>
        </ul>
    </nav>
</div>
