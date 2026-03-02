<?php
/**
 * Alumni Directory - Stitch "Professional Directory" UI
 */
if (!defined('ABSPATH')) exit;

$current_uid = get_current_user_id();
$is_guest = ! $current_uid;
// Search support
$search_q = isset($_GET['search_alumni']) ? sanitize_text_field($_GET['search_alumni']) : '';
if (!empty($search_q)) {
    $args = [
        'role'        => 'mtts_alumni',
        'search'      => '*' . $search_q . '*',
        'search_columns' => ['user_login', 'display_name', 'user_email'],
    ];
} else {
    $args = [
        'role'    => 'mtts_alumni',
        'orderby' => 'display_name',
        'order'   => 'ASC',
        'number'  => 36,
    ];
}
$alumni_query = new \WP_User_Query($args);
$alumni       = $alumni_query->get_results();
?>

<style>
.st-dir-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 28px;
}
.st-search-bar {
    display: flex;
    align-items: center;
    background: #fff;
    border: 1.5px solid #e5e7eb;
    border-radius: 100px;
    padding: 10px 18px;
    gap: 10px;
    box-shadow: 0 1px 4px rgba(13,39,165,.06);
    width: 360px;
    max-width: 100%;
}
.st-search-bar input {
    border: none;
    outline: none;
    font-size: 14px;
    color: #1a1a2e;
    width: 100%;
    background: transparent;
}
.st-dir-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 20px;
}
.st-dir-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(13,39,165,.08);
    overflow: hidden;
    text-align: center;
    transition: box-shadow 0.2s, transform 0.2s;
}
.st-dir-card:hover {
    box-shadow: 0 6px 24px rgba(13,39,165,.14);
    transform: translateY(-3px);
}
.st-dir-card-banner {
    height: 70px;
    background: linear-gradient(135deg, #6b21a8, #7c3aed);
}
.st-dir-card-body { padding: 0 20px 20px; }
.st-dir-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    border: 3px solid #fff;
    object-fit: cover;
    margin-top: -40px;
    box-shadow: 0 4px 14px rgba(0,0,0,.12);
}
.st-dir-actions { display: flex; gap: 8px; justify-content: center; margin-top: 14px; }
</style>

<div style="max-width:1200px;margin:0 auto;">
    <div class="st-dir-header">
        <div>
            <h2 style="margin:0;font-size:26px;font-weight:800;color:#1a1a2e;">Alumni Directory</h2>
            <p style="color:#6b7280;margin:5px 0 0;font-size:14px;">Connecting ministers and scholars across the globe.</p>
        </div>
        <form method="get" action="" style="display:contents;">
            <?php foreach ($_GET as $k => $v):
                if ($k === 'search_alumni') continue;
            ?>
            <input type="hidden" name="<?php echo esc_attr($k); ?>" value="<?php echo esc_attr($v); ?>">
            <?php endforeach; ?>
            <div class="st-search-bar">
                <span class="dashicons dashicons-search" style="color:#9ca3af;"></span>
                <input type="text" name="search_alumni" value="<?php echo esc_attr($search_q); ?>" placeholder="Search ministers and scholars...">
                <?php if ($search_q): ?>
                <a href="?view=directory" style="color:#6b7280;text-decoration:none;font-size:18px;">&times;</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($alumni)): ?>
    <div style="text-align:center;padding:80px 20px;background:#fff;border-radius:12px;border:1px solid #e5e7eb;">
        <div style="font-size:48px;margin-bottom:16px;">🌱</div>
        <h3 style="color:#1a1a2e;margin:0 0 8px;">
            <?php echo $search_q ? 'No results for "' . esc_html($search_q) . '"' : 'The fellowship is growing'; ?>
        </h3>
        <p style="color:#6b7280;margin:0 0 20px;">
            <?php echo $search_q ? 'Try a different search term.' : 'Be the first to reach out!'; ?>
        </p>
        <?php if ($search_q): ?>
        <a href="?view=directory" class="stitch-btn-primary">← Clear Search</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="st-dir-grid">
        <?php foreach ($alumni as $alum):
            $alp = \MttsLms\Models\AlumniProfile::get_by_user($alum->ID);
            $req = \MttsLms\Models\FriendRequest::get_request($current_uid, $alum->ID);
            if ($alum->ID === $current_uid) continue; // skip yourself
        ?>
        <div class="st-dir-card">
            <div class="st-dir-card-banner" style="<?php echo $alp->banner_url ? 'background:url('.esc_url($alp->banner_url).') center/cover;' : ''; ?>"></div>
            <div class="st-dir-card-body">
                <img src="<?php echo esc_url($alp->profile_picture_url ?: get_avatar_url($alum->ID)); ?>" class="st-dir-avatar" alt="<?php echo esc_attr($alum->display_name); ?>">
                <h4 style="margin:12px 0 4px;font-size:16px;color:#1a1a2e;"><?php echo esc_html($alum->display_name); ?></h4>
                <p style="color:#6b21a8;font-weight:600;font-size:12px;margin:0 0 4px;"><?php echo esc_html($alp->headline ?: 'Minister of the Gospel'); ?></p>
                <p style="color:#6b7280;font-size:12px;margin:0 0 6px;">
                    <?php if ($alp->location): ?>📍 <?php echo esc_html($alp->location); ?><?php endif; ?>
                </p>
                <?php
                $grad_y = $alp->graduation_year ?: get_user_meta($alum->ID, 'mtts_graduation_year', true);
                if ($grad_y): ?>
                <span style="display:inline-block;background:#f3f4f8;border-radius:100px;padding:2px 10px;font-size:11px;color:#6b7280;margin-bottom:8px;">Class of <?php echo esc_html($grad_y); ?></span>
                <?php endif; ?>

                <div class="st-dir-actions">
                    <a href="?view=profile&uid=<?php echo $alum->ID; ?>" class="stitch-btn-primary" style="flex:1;justify-content:center;padding:7px 12px;font-size:12px;border-radius:8px;text-decoration:none;">View Profile</a>

                    <?php if ( ! $is_guest ) : ?>
                        <?php if ($req && $req->status === 'accepted'): ?>
                            <a href="?view=messenger&chat_with=<?php echo $alum->ID; ?>" class="stitch-btn-outline" style="flex:1;justify-content:center;padding:7px 12px;font-size:12px;border-radius:8px;">💬 Chat</a>
                        <?php elseif ($req && $req->status === 'pending'): ?>
                            <span class="stitch-btn-outline" style="flex:1;justify-content:center;padding:7px 12px;font-size:12px;border-radius:8px;cursor:default;color:#9ca3af;border-color:#e5e7eb;">Pending</span>
                        <?php else: ?>
                            <button type="button" class="stitch-btn-outline" style="flex:1;justify-content:center;padding:7px 12px;font-size:12px;border-radius:8px;" onclick="stDirConnect(<?php echo $alum->ID; ?>, this)">🤝 Connect</button>
                        <?php endif; ?>
                    <?php else : ?>
                        <a href="<?php echo wp_login_url(get_permalink()); ?>" class="stitch-btn-outline" style="flex:1;justify-content:center;padding:7px 12px;font-size:12px;border-radius:8px; opacity:0.6;">Connect</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function stDirConnect(uid, btn) {
    const fd = new FormData();
    fd.append('mtts_alumni_action', 'send_friend_request');
    fd.append('receiver_id', uid);
    fd.append('_wpnonce', '<?php echo wp_create_nonce("mtts_alumni_social"); ?>');
    btn.disabled = true;
    btn.textContent = '✓ Sent';
    btn.style.color = '#6b7280';
    btn.style.borderColor = '#e5e7eb';
    fetch('', { method: 'POST', body: fd })
    .then(r => r.json())
    .catch(() => { btn.disabled = false; btn.textContent = '🤝 Connect'; });
}
</script>
