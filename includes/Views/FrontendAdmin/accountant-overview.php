<?php
/**
 * Accountant Dashboard Overview
 */
global $wpdb;
$transactions_table = $wpdb->prefix . 'mtts_transactions';
$total_paid    = (float) $wpdb->get_var("SELECT SUM(amount) FROM {$transactions_table} WHERE status = 'paid'");
$total_pending = (int)   $wpdb->get_var("SELECT COUNT(*) FROM {$transactions_table} WHERE status = 'pending'");
$total_txns    = (int)   $wpdb->get_var("SELECT COUNT(*) FROM {$transactions_table}");
$unpaid_apps   = (int)   $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mtts_applications WHERE payment_status = 'unpaid'");

$cards = [
    ['label' => 'Total Revenue (₦)',    'value' => number_format($total_paid, 2), 'icon' => 'dashicons-chart-line', 'color' => '#10b981', 'url' => '?view=transactions'],
    ['label' => 'Pending Transactions', 'value' => $total_pending, 'icon' => 'dashicons-clock',      'color' => '#f59e0b', 'url' => '?view=transactions'],
    ['label' => 'All Transactions',     'value' => $total_txns,    'icon' => 'dashicons-money-alt',  'color' => '#3b82f6', 'url' => '?view=transactions'],
    ['label' => 'Unpaid Applications',  'value' => $unpaid_apps,   'icon' => 'dashicons-warning',    'color' => '#ef4444', 'url' => '?view=payments'],
];
?>
<div class="mtts-admin-portal-header" style="margin-bottom:30px;">
    <h2 style="font-size:1.8rem; font-weight:800;">Financial Portal</h2>
    <p style="opacity:0.7;">Monitor payments, revenue, and financial records for the institution.</p>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; margin-bottom:35px;">
    <?php foreach ($cards as $card): ?>
    <a href="<?php echo esc_attr($card['url']); ?>" style="text-decoration:none;">
        <div class="koinonia-glass" style="padding:25px; border-radius:16px; display:flex; align-items:center; gap:20px; transition:all 0.3s;">
            <div style="background:<?php echo $card['color']; ?>20; width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <span class="dashicons <?php echo $card['icon']; ?>" style="color:<?php echo $card['color']; ?>; font-size:24px; width:24px; height:24px;"></span>
            </div>
            <div>
                <div style="font-size:1.8rem; font-weight:800; line-height:1;"><?php echo esc_html($card['value']); ?></div>
                <div style="font-size:0.85rem; opacity:0.7; margin-top:4px;"><?php echo esc_html($card['label']); ?></div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<div class="koinonia-glass" style="border-radius:16px; padding:25px;">
    <h3 style="margin-bottom:20px; font-weight:700;">⚡ Quick Actions</h3>
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:15px;">
        <a href="?view=transactions" class="mtts-btn mtts-btn-primary" style="text-align:center;">🔄 View Transactions</a>
        <a href="?view=payments" class="mtts-btn" style="text-align:center; background:rgba(255,255,255,0.1);">💳 Unpaid Fees</a>
        <a href="<?php echo esc_url(home_url('/alumni-network')); ?>" class="mtts-btn" style="text-align:center; background:rgba(167,139,250,0.2); color:#a78bfa;">🌐 Alumni Network</a>
    </div>
</div>
