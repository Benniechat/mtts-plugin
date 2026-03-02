<?php
/**
 * School Admin Dashboard Overview
 */
global $wpdb;
$users_table    = $wpdb->prefix . 'mtts_students';
$total_students = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$users_table}");
$total_lecturers = count(get_users(['role' => 'mtts_lecturer', 'fields' => 'ID']));
$total_apps     = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mtts_applications WHERE status = 'pending'");
$total_programs = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mtts_programs");

$cards = [
    ['label' => 'Total Students', 'value' => $total_students, 'icon' => 'dashicons-groups', 'color' => '#7c3aed', 'url' => '?view=students'],
    ['label' => 'Lecturers', 'value' => $total_lecturers, 'icon' => 'dashicons-welcome-learn-more', 'color' => '#3b82f6', 'url' => '?view=lecturers'],
    ['label' => 'Pending Applications', 'value' => $total_apps, 'icon' => 'dashicons-clipboard', 'color' => '#f59e0b', 'url' => '?view=applications'],
    ['label' => 'Active Programs', 'value' => $total_programs, 'icon' => 'dashicons-book-alt', 'color' => '#10b981', 'url' => '?view=programs'],
];
?>
<div class="mtts-admin-portal-header" style="margin-bottom:30px;">
    <h2 style="font-size:1.8rem; font-weight:800;">School Administration Portal</h2>
    <p style="opacity:0.7;">Welcome back, <?php echo esc_html(wp_get_current_user()->display_name); ?>. Here's your institution at a glance.</p>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; margin-bottom:35px;">
    <?php foreach ($cards as $card): ?>
    <a href="<?php echo esc_attr($card['url']); ?>" style="text-decoration:none;">
        <div class="mtts-stat-card koinonia-glass" style="padding:25px; border-radius:16px; display:flex; align-items:center; gap:20px; transition:all 0.3s; cursor:pointer;">
            <div style="background:<?php echo $card['color']; ?>20; width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <span class="dashicons <?php echo $card['icon']; ?>" style="color:<?php echo $card['color']; ?>; font-size:24px; width:24px; height:24px;"></span>
            </div>
            <div>
                <div style="font-size:2rem; font-weight:800; line-height:1;"><?php echo esc_html($card['value']); ?></div>
                <div style="font-size:0.85rem; opacity:0.7; margin-top:4px;"><?php echo esc_html($card['label']); ?></div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<!-- Quick Action Links -->
<div class="koinonia-glass" style="border-radius:16px; padding:25px;">
    <h3 style="margin-bottom:20px; font-weight:700;">⚡ Quick Actions</h3>
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:15px;">
        <a href="?view=applications" class="mtts-btn mtts-btn-primary" style="text-align:center;">📋 Review Applications</a>
        <a href="?view=students" class="mtts-btn" style="text-align:center; background:rgba(255,255,255,0.1);">🎓 Manage Students</a>
        <a href="<?php echo esc_url(home_url('/alumni-network')); ?>" class="mtts-btn" style="text-align:center; background:rgba(167,139,250,0.2); color:#a78bfa;">🌐 Visit Alumni Network</a>
    </div>
</div>
