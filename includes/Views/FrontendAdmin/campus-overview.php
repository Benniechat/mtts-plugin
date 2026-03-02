<?php
/**
 * Campus Coordinator Dashboard Overview
 */
global $wpdb;
$user     = wp_get_current_user();

// Try to determine the user's assigned campus via user meta
$campus_id = get_user_meta($user->ID, 'mtts_campus_center_id', true);
$campus    = $campus_id ? \MttsLms\Models\CampusCenter::find($campus_id) : null;

$campus_students = 0;
if ($campus_id) {
    $campus_students = (int) $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}mtts_students WHERE campus_center_id = %d", $campus_id)
    );
} else {
    $campus_students = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mtts_students");
}

$total_programs = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mtts_programs");

$cards = [
    ['label' => 'My Students',      'value' => $campus_students, 'icon' => 'dashicons-groups',   'color' => '#7c3aed', 'url' => '?view=students'],
    ['label' => 'Active Programs',  'value' => $total_programs,  'icon' => 'dashicons-book-alt',  'color' => '#10b981', 'url' => '#'],
    ['label' => 'Campus',           'value' => $campus ? $campus->name : 'All Campuses', 'icon' => 'dashicons-location', 'color' => '#3b82f6', 'url' => '#'],
];
?>
<div class="mtts-admin-portal-header" style="margin-bottom:30px;">
    <h2 style="font-size:1.8rem; font-weight:800;">Campus Coordinator Portal</h2>
    <p style="opacity:0.7;">
        <?php echo $campus ? 'Managing: <strong>' . esc_html($campus->name) . '</strong>' : 'Managing: All Campuses'; ?>
    </p>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; margin-bottom:35px;">
    <?php foreach ($cards as $card): ?>
    <a href="<?php echo esc_attr($card['url']); ?>" style="text-decoration:none;">
        <div class="koinonia-glass" style="padding:25px; border-radius:16px; display:flex; align-items:center; gap:20px; transition:all 0.3s;">
            <div style="background:<?php echo $card['color']; ?>20; width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <span class="dashicons <?php echo $card['icon']; ?>" style="color:<?php echo $card['color']; ?>; font-size:24px; width:24px; height:24px;"></span>
            </div>
            <div>
                <div style="font-size:1.8rem; font-weight:800; line-height:1; word-break:break-word;"><?php echo esc_html($card['value']); ?></div>
                <div style="font-size:0.85rem; opacity:0.7; margin-top:4px;"><?php echo esc_html($card['label']); ?></div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<div class="koinonia-glass" style="border-radius:16px; padding:25px;">
    <h3 style="margin-bottom:20px; font-weight:700;">⚡ Quick Actions</h3>
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:15px;">
        <a href="?view=students" class="mtts-btn mtts-btn-primary" style="text-align:center;">🎓 My Students</a>
        <a href="?view=attendance" class="mtts-btn" style="text-align:center; background:rgba(255,255,255,0.1);">✅ Attendance</a>
        <a href="<?php echo esc_url(home_url('/alumni-network')); ?>" class="mtts-btn" style="text-align:center; background:rgba(167,139,250,0.2); color:#a78bfa;">🌐 Alumni Network</a>
    </div>
</div>
