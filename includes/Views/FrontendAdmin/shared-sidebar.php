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

<!-- Left Sidebar Column: LinkedIn Style -->
<div class="lms-sidebar-card">
    <div class="lms-side-user-info">
        <img src="<?php echo get_avatar_url( $user->ID ); ?>" alt="Profile">
        <h3 style="font-size:16px; margin: 4px 0;">Staff Portal</h3>
        <small style="color:var(--lms-text-sub); display:block; font-size:12px;"><?php echo esc_html( implode(', ', array_map('ucwords', str_replace('mtts_', '', $roles)))); ?></small>
    </div>

    <ul class="lms-side-nav-list">
        <?php foreach ($nav_links as $key => $portal): ?>
            <?php
            $is_active_portal = (strpos($current_slug, $key) !== false);
            $active_views = $is_active_portal ? $portal['views'] : [];
            ?>
            <li>
                <a href="<?php echo esc_url($portal['url']); ?>" class="<?php echo $is_active_portal ? 'active' : ''; ?>">
                    <span class="dashicons <?php echo $portal['icon']; ?>"></span> <?php echo $portal['label']; ?>
                </a>
                <?php if ($is_active_portal && !empty($active_views)): ?>
                <ul style="padding-left:15px; list-style:none; margin: 5px 0 10px 10px; border-left: 1px solid var(--lms-border);">
                    <?php foreach ($active_views as $v => $label): ?>
                    <li>
                        <a href="?view=<?php echo $v; ?>" class="<?php echo $view === $v ? 'active' : ''; ?>" style="font-size:13px; padding: 6px 12px; font-weight: normal;">
                            <?php echo $label; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Community/System Card -->
<div class="lms-sidebar-card" style="margin-top: 12px;">
    <ul class="lms-side-nav-list">
        <li>
            <a href="?view=security" style="color:var(--lms-text);">
                <span class="dashicons dashicons-shield"></span> Security Settings
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url(home_url('/alumni-network')); ?>" style="color:var(--lms-purple);">
                <span class="dashicons dashicons-groups"></span> Alumni Network
            </a>
        </li>
        <li>
            <a href="<?php echo wp_logout_url(home_url()); ?>" style="color:var(--mfm-danger);">
                <span class="dashicons dashicons-exit"></span> Logout
            </a>
        </li>
    </ul>
</div>

