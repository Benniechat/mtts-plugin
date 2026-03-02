<?php
/**
 * Alumni Public Profile View - Stitch UI Design
 */
$view_user_id  = isset($_GET['uid']) ? intval($_GET['uid']) : get_current_user_id();
$current_uid   = get_current_user_id();
$is_own        = ($view_user_id === $current_uid);
$viewing_user  = get_userdata($view_user_id);
$profile       = \MttsLms\Models\AlumniProfile::get_by_user($view_user_id);
$student       = \MttsLms\Models\Student::get_by_user($view_user_id);

// Friends / connection status
$conn_request  = \MttsLms\Models\FriendRequest::get_request($current_uid, $view_user_id);
$friend_count  = count((array)\MttsLms\Models\FriendRequest::get_friends($view_user_id));

// Non-sensitive info from student record
$graduation_year = $profile->graduation_year ?? get_user_meta($view_user_id, 'mtts_graduation_year', true) ?? '';
$matric          = ''; // Sensitive — omit from public profile
$program_name    = '';
if ($student) {
    global $wpdb;
    $prog_table = $wpdb->prefix . 'mtts_programs';
    $prog = $wpdb->get_row($wpdb->prepare("SELECT * FROM $prog_table WHERE id = %d", $student->program_id ?? 0));
    $program_name = $prog->name ?? '';
}

// Interests tags
$interests_arr = !empty($profile->interests) ? array_filter(array_map('trim', explode(',', $profile->interests))) : [];
$gifts_arr     = !empty($profile->gifts_graces) ? array_filter(array_map('trim', explode(',', $profile->gifts_graces))) : [];

// Mutual connections
global $wpdb;
$mutual = [];
if (!$is_own) {
    $my_friends    = \MttsLms\Models\FriendRequest::get_friends($current_uid);
    $their_friends = \MttsLms\Models\FriendRequest::get_friends($view_user_id);
    $my_fids       = array_map(fn($r) => ($r->sender_id == $current_uid ? $r->receiver_id : $r->sender_id), (array)$my_friends);
    $their_fids    = array_map(fn($r) => ($r->sender_id == $view_user_id ? $r->receiver_id : $r->sender_id), (array)$their_friends);
    $mutual_ids    = array_intersect($my_fids, $their_fids);
    foreach (array_slice($mutual_ids, 0, 3) as $mid) {
        $mu = get_userdata($mid);
        $mp = \MttsLms\Models\AlumniProfile::get_by_user($mid);
        if ($mu) $mutual[] = ['user' => $mu, 'profile' => $mp];
    }
}

// Publications from ministry_milestones
$milestones_raw = $profile->ministry_milestones ?? '';
$milestones     = array_filter(array_map('trim', explode("\n", $milestones_raw)));
?>

<style>
.st-profile-header {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(13,39,165,.08);
    border: 1px solid #e5e7eb;
    margin-bottom: 20px;
}
.st-profile-banner {
    height: 200px;
    background: linear-gradient(135deg, #0d27a5, #3b5bdb, #7c3aed);
    position: relative;
    overflow: hidden;
}
.st-profile-banner img {
    width: 100%; height: 100%;
    object-fit: cover;
}
.st-profile-avatar {
    width: 120px; height: 120px;
    border-radius: 50%;
    border: 4px solid #fff;
    object-fit: cover;
    position: absolute;
    bottom: -60px;
    left: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
}
.st-profile-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    max-width: 1000px;
    margin: 0 auto;
    align-items: start;
}
.st-section-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(13,39,165,.08);
    padding: 24px;
    margin-bottom: 16px;
}
.st-section-card h4 {
    margin: 0 0 16px;
    color: #1a1a2e;
    font-size: 17px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}
.st-badge {
    display: inline-block;
    background: #e8ecfd;
    color: #0d27a5;
    border-radius: 100px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    margin: 3px;
}
.st-timeline-item {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f3f4f8;
}
.st-timeline-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.st-timeline-icon {
    width: 44px; height: 44px;
    border-radius: 8px;
    background: #e8ecfd;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.mutual-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; margin-right: -8px; }
@media(max-width:768px) { .st-profile-layout { grid-template-columns: 1fr; } }
</style>

<div style="max-width:1000px;margin:0 auto;">
    <!-- Profile Header -->
    <div class="st-profile-header">
        <div class="st-profile-banner" style="position:relative;">
            <?php if ($profile->banner_url): ?>
                <img src="<?php echo esc_url($profile->banner_url); ?>">
            <?php endif; ?>
            <img src="<?php echo esc_url($profile->profile_picture_url ?: get_avatar_url($view_user_id)); ?>" class="st-profile-avatar">
        </div>
        <div style="padding: 70px 30px 24px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;">
                <div>
                    <h1 style="margin:0 0 4px;font-size:26px;color:#1a1a2e;"><?php echo esc_html($viewing_user->display_name); ?></h1>
                    <p style="font-size:16px;color:#374151;margin:0 0 6px;font-weight:500;"><?php echo esc_html($profile->headline ?: 'Theologian & Minister'); ?></p>
                    <p style="font-size:13px;color:#6b7280;margin:0 0 6px;">
                        <?php if ($profile->location): ?>
                        📍 <?php echo esc_html($profile->location); ?> &nbsp;•&nbsp;
                        <?php endif; ?>
                        <span style="color:#0d27a5;font-weight:600;"><?php echo $friend_count; ?> Connection<?php echo $friend_count !== 1 ? 's' : ''; ?></span>
                    </p>
                    <?php if ($program_name || $graduation_year): ?>
                    <p style="font-size:12px;color:#9ca3af;margin:4px 0 0;">
                        <?php if ($program_name): ?> 🎓 <?php echo esc_html($program_name); ?><?php endif; ?>
                        <?php if ($graduation_year): ?> • Class of <?php echo esc_html($graduation_year); ?><?php endif; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <?php if ($is_own): ?>
                        <a href="?view=profile-edit" class="stitch-btn-primary">✏️ Edit Profile</a>
                        <a href="?view=messenger" class="stitch-btn-outline">💬 Messages</a>
                    <?php else:
                        if ($conn_request && $conn_request->status === 'accepted'): ?>
                            <a href="?view=messenger&chat_with=<?php echo $view_user_id; ?>" class="stitch-btn-primary">💬 Message</a>
                            <span class="stitch-btn-outline" style="cursor:default;border-color:#2e7d32;color:#2e7d32;">✓ Connected</span>
                        <?php elseif ($conn_request && $conn_request->status === 'pending'): ?>
                            <span class="stitch-btn-outline" style="cursor:default;color:#6b7280;border-color:#e5e7eb;">Pending Request</span>
                        <?php else: ?>
                            <button class="stitch-btn-primary" onclick="stConnectProfile(<?php echo $view_user_id; ?>, this)">🤝 Connect</button>
                            <a href="?view=messenger&chat_with=<?php echo $view_user_id; ?>" class="stitch-btn-outline">💬 Message</a>
                        <?php endif;
                    endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="st-profile-layout">
        <!-- Left Column -->
        <div>
            <!-- About -->
            <div class="st-section-card">
                <h4>📖 About</h4>
                <p style="font-size:15px;line-height:1.75;color:#374151;margin:0;">
                    <?php echo nl2br(esc_html($profile->bio ?: 'No bio provided yet. Edit your profile to add a ministry summary.')); ?>
                </p>
            </div>

            <!-- Ministry & Experience -->
            <div class="st-section-card">
                <h4>✝ Ministry Journey</h4>
                <div class="st-timeline-item">
                    <div class="st-timeline-icon">
                        <span class="dashicons dashicons-building" style="color:#0d27a5;"></span>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#1a1a2e;font-size:15px;"><?php echo esc_html($profile->current_ministry ?: 'Active Service'); ?></div>
                        <div style="font-size:13px;color:#6b7280;">Current Ministry Position</div>
                        <?php if (!empty($profile->experience)): ?>
                        <div style="font-size:14px;color:#374151;margin-top:6px;line-height:1.6;"><?php echo nl2br(esc_html(substr($profile->experience, 0, 300))); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Milestones -->
                <?php if (!empty($milestones)): ?>
                <?php foreach (array_slice($milestones, 0, 3) as $ms): ?>
                <div class="st-timeline-item">
                    <div class="st-timeline-icon">
                        <span class="dashicons dashicons-star-filled" style="color:#f59e0b;"></span>
                    </div>
                    <div>
                        <div style="font-size:14px;color:#374151;"><?php echo esc_html($ms); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Education -->
            <div class="st-section-card">
                <h4>🎓 Education</h4>
                <div class="st-timeline-item">
                    <div class="st-timeline-icon">
                        <span class="dashicons dashicons-welcome-learn-more" style="color:#0d27a5;"></span>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#1a1a2e;font-size:15px;"><?php echo esc_html($program_name ?: 'Mid-Town Theological Seminary (MTTS)'); ?></div>
                        <div style="font-size:13px;color:#6b7280;">
                            Mid-Town Theological Seminary<?php echo $graduation_year ? ' • Class of ' . esc_html($graduation_year) : ''; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Publications if any milestones look like articles -->
            <?php if (!empty($profile->ministry_milestones)): ?>
            <div class="st-section-card">
                <h4>📄 Theological Publications</h4>
                <?php foreach ($milestones as $ms): ?>
                <div style="padding:12px 0;border-bottom:1px solid #f3f4f8;font-size:14px;color:#374151;line-height:1.6;">📌 <?php echo esc_html($ms); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Spiritual Interests -->
            <div class="st-section-card">
                <h4>🌟 Spiritual Interests</h4>
                <?php if (!empty($interests_arr)): ?>
                    <?php foreach ($interests_arr as $int): ?>
                        <span class="st-badge"><?php echo esc_html($int); ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#9ca3af;font-size:13px;margin:0;">No interests listed yet.</p>
                <?php endif; ?>
            </div>

            <!-- Gifts & Graces -->
            <?php if (!empty($gifts_arr)): ?>
            <div class="st-section-card">
                <h4>🕊 Gifts & Graces</h4>
                <?php foreach ($gifts_arr as $g): ?>
                    <span class="st-badge" style="background:#f3e8ff;color:#7c3aed;"><?php echo esc_html($g); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Network Activity -->
            <div class="st-section-card">
                <h4>📊 Network Activity</h4>
                <div style="display:flex;gap:20px;text-align:center;">
                    <div>
                        <div style="font-size:24px;font-weight:800;color:#0d27a5;"><?php echo $friend_count; ?></div>
                        <div style="font-size:12px;color:#6b7280;">Connections</div>
                    </div>
                    <div>
                        <div style="font-size:24px;font-weight:800;color:#0d27a5;">
                            <?php echo count((array)\MttsLms\Models\AlumniPost::get_posts_by_user($view_user_id, 99)); ?>
                        </div>
                        <div style="font-size:12px;color:#6b7280;">Posts</div>
                    </div>
                </div>
            </div>

            <!-- Mutual Connections -->
            <?php if (!empty($mutual)): ?>
            <div class="st-section-card">
                <h4>🤝 Mutual Connections</h4>
                <?php foreach ($mutual as $m): ?>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <img src="<?php echo esc_url($m['profile']->profile_picture_url ?: get_avatar_url($m['user']->ID)); ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                    <div>
                        <a href="?view=profile&uid=<?php echo $m['user']->ID; ?>" style="font-weight:700;font-size:13px;color:#1a1a2e;text-decoration:none;"><?php echo esc_html($m['user']->display_name); ?></a>
                        <div style="font-size:11px;color:#6b7280;">MTTS <?php echo esc_html($m['profile']->graduation_year ?: 'Alumni'); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function stConnectProfile(uid, btn) {
    const fd = new FormData();
    fd.append('mtts_alumni_action', 'send_friend_request');
    fd.append('receiver_id', uid);
    fd.append('_wpnonce', '<?php echo wp_create_nonce("mtts_alumni_social"); ?>');
    btn.disabled = true;
    btn.textContent = 'Request Sent!';
    fetch('', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (!d.success) { btn.disabled = false; btn.textContent = '🤝 Connect'; } })
    .catch(() => { btn.disabled = false; btn.textContent = '🤝 Connect'; });
}
</script>
