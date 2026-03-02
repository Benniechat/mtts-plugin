<?php
/**
 * Registrar Dashboard Overview
 */
global $wpdb;
$pending_apps  = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mtts_applications WHERE status = 'pending'");
$approved_apps = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mtts_applications WHERE status = 'approved'");
$total_students= (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mtts_students");
$sessions      = \MttsLms\Models\Session::get_active_session();

$cards = [
    ['label' => 'Pending Applications', 'value' => $pending_apps,  'icon' => 'dashicons-clock',    'color' => '#f59e0b', 'url' => '?view=applications'],
    ['label' => 'Approved Applications','value' => $approved_apps, 'icon' => 'dashicons-yes-alt',  'color' => '#10b981', 'url' => '?view=applications'],
    ['label' => 'Total Students',       'value' => $total_students,'icon' => 'dashicons-groups',   'color' => '#7c3aed', 'url' => '?view=students'],
    ['label' => 'Active Session',       'value' => $sessions ? 'Active' : 'None','icon' => 'dashicons-calendar-alt','color' => '#3b82f6', 'url' => '#'],
];
?>
<div class="mtts-admin-portal-header" style="margin-bottom:30px;">
    <h2 style="font-size:1.8rem; font-weight:800;">Registrar Portal</h2>
    <p style="opacity:0.7;">Manage student admissions, matriculation, and academic records.</p>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; margin-bottom:35px;">
    <?php foreach ($cards as $card): ?>
    <a href="<?php echo esc_attr($card['url']); ?>" style="text-decoration:none;">
        <div class="koinonia-glass" style="padding:25px; border-radius:16px; display:flex; align-items:center; gap:20px; transition:all 0.3s;">
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

<div class="koinonia-glass" style="border-radius:16px; padding:25px;">
    <h3 style="margin-bottom:20px; font-weight:700;">⚡ Quick Actions</h3>
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:15px;">
        <a href="?view=applications" class="mtts-btn mtts-btn-primary" style="text-align:center;">📋 View Applications</a>
        <a href="?view=students" class="mtts-btn" style="text-align:center; background:rgba(255,255,255,0.1);">🎓 Student Records</a>
        <a href="<?php echo esc_url(home_url('/alumni-network')); ?>" class="mtts-btn" style="text-align:center; background:rgba(167,139,250,0.2); color:#a78bfa;">🌐 Alumni Network</a>
    </div>
</div>
