<?php
/**
 * Fellowship Circle – Connection Requests & Friends List
 * Stitch UI Design
 */
if (!defined('ABSPATH')) exit;
$current_uid = get_current_user_id();
?>

<style>
.st-friends-layout {
    max-width: 1000px;
    margin: 0 auto;
}
.st-req-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(13,39,165,.08);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 20px;
    gap: 8px;
    transition: box-shadow 0.2s;
}
.st-req-card:hover { box-shadow: 0 6px 20px rgba(13,39,165,.12); }
.st-req-avatar { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #f5f3ff; }
.st-friend-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid #f3f4f8;
    transition: background 0.15s;
}
.st-friend-row:hover { background: #f9fafb; }
.st-friend-row:last-child { border-bottom: none; }
.st-friend-avatar { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2px solid #f5f3ff; }
.st-section-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(13,39,165,.08);
    margin-bottom: 24px;
    overflow: hidden;
}
.st-section-card-header {
    padding: 18px 24px 14px;
    border-bottom: 1px solid #f3f4f8;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>

<div class="st-friends-layout">
    <div style="margin-bottom:32px;">
        <h2 style="margin:0;font-size:26px;font-weight:800;color:#1a1a2e;">Fellowship Circle</h2>
        <p style="color:#6b7280;margin:5px 0 0;font-size:14px;">Manage your covenant connections and pending invitations.</p>
    </div>

    <!-- PENDING REQUESTS -->
    <?php if (!empty($pending_requests)): ?>
    <div class="st-section-card" style="margin-bottom:32px;border-left:4px solid #6b21a8;">
        <div class="st-section-card-header">
            <h3 style="margin:0;font-size:17px;color:#1a1a2e;font-weight:700;">
                📬 Pending Invitations
            </h3>
            <span style="background:#f5f3ff;color:#6b21a8;border-radius:100px;padding:3px 12px;font-size:12px;font-weight:700;"><?php echo count($pending_requests); ?> New</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;padding:20px;">
            <?php foreach ($pending_requests as $req):
                $sender = get_userdata($req->sender_id);
                $sender_profile = \MttsLms\Models\AlumniProfile::get_by_user($req->sender_id);
                if (!$sender) continue;
            ?>
            <div class="st-req-card">
                <a href="?view=profile&uid=<?php echo $req->sender_id; ?>">
                    <img src="<?php echo esc_url($sender_profile->profile_picture_url ?: get_avatar_url($req->sender_id)); ?>" class="st-req-avatar">
                </a>
                <a href="?view=profile&uid=<?php echo $req->sender_id; ?>" style="font-weight:700;font-size:14px;color:#1a1a2e;text-decoration:none;"><?php echo esc_html($sender->display_name); ?></a>
                <div style="font-size:12px;color:#6b21a8;font-weight:600;"><?php echo esc_html(substr($sender_profile->headline ?: 'MTTS Alumni', 0, 36)); ?></div>
                <?php
                $mutual_count = 0;
                $my_friends    = \MttsLms\Models\FriendRequest::get_friends($current_uid);
                $their_friends = \MttsLms\Models\FriendRequest::get_friends($req->sender_id);
                $my_ids   = array_map(fn($r) => ($r->sender_id == $current_uid ? $r->receiver_id : $r->sender_id), (array)$my_friends);
                $their_ids = array_map(fn($r) => ($r->sender_id == $req->sender_id ? $r->receiver_id : $r->sender_id), (array)$their_friends);
                $mutual_count = count(array_intersect($my_ids, $their_ids));
                if ($mutual_count > 0):
                ?>
                <div style="font-size:11px;color:#6b7280;"><?php echo $mutual_count; ?> mutual connection<?php echo $mutual_count > 1 ? 's' : ''; ?></div>
                <?php endif; ?>

                <div style="display:flex;gap:8px;width:100%;margin-top:6px;">
                    <form method="post" action="" style="flex:1;">
                        <?php wp_nonce_field('mtts_alumni_social'); ?>
                        <input type="hidden" name="mtts_alumni_action" value="accept_friend_request">
                        <input type="hidden" name="request_id" value="<?php echo $req->id; ?>">
                        <button type="submit" class="stitch-btn-primary" style="width:100%;justify-content:center;border-radius:8px;padding:8px;">✓ Accept</button>
                    </form>
                    <form method="post" action="" style="flex:1;">
                        <?php wp_nonce_field('mtts_alumni_social'); ?>
                        <input type="hidden" name="mtts_alumni_action" value="reject_friend_request">
                        <input type="hidden" name="request_id" value="<?php echo $req->id; ?>">
                        <button type="submit" class="stitch-btn-outline" style="width:100%;justify-content:center;border-radius:8px;padding:7px;background:#f3f4f8;border-color:#e5e7eb;color:#6b7280;">✕ Decline</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- FRIENDS LIST -->
    <div class="st-section-card">
        <div class="st-section-card-header">
            <h3 style="margin:0;font-size:17px;color:#1a1a2e;font-weight:700;">👥 Active Connections</h3>
            <span style="background:#f3f4f8;color:#6b7280;border-radius:100px;padding:3px 12px;font-size:12px;font-weight:700;"><?php echo count((array)$friends_data); ?> connected</span>
        </div>

        <?php if (empty($friends_data)): ?>
        <div style="text-align:center;padding:60px 20px;color:#9ca3af;">
            <div style="font-size:48px;margin-bottom:16px;">🌾</div>
            <p style="font-size:16px;margin:0 0 16px;">The harvest is plenty, but your connections are few.</p>
            <a href="?view=directory" class="stitch-btn-primary">Explore the Alumni Directory →</a>
        </div>
        <?php else: ?>
        <?php foreach ($friends_data as $fr):
            $friend_id = ($fr->sender_id == $user->ID) ? $fr->receiver_id : $fr->sender_id;
            $friend = get_userdata($friend_id);
            $fprofile = \MttsLms\Models\AlumniProfile::get_by_user($friend_id);
            if (!$friend) continue;
        ?>
        <div class="st-friend-row">
            <a href="?view=profile&uid=<?php echo $friend_id; ?>">
                <img src="<?php echo esc_url($fprofile->profile_picture_url ?: get_avatar_url($friend_id)); ?>" class="st-friend-avatar">
            </a>
            <div style="flex:1;min-width:0;">
                <a href="?view=profile&uid=<?php echo $friend_id; ?>" style="font-weight:700;font-size:14px;color:#1a1a2e;text-decoration:none;"><?php echo esc_html($friend->display_name); ?></a>
                <div style="font-size:12px;color:#6b21a8;font-weight:600;"><?php echo esc_html(substr($fprofile->headline ?: 'MTTS Alumni', 0, 40)); ?></div>
                <?php if ($fprofile->location): ?>
                <div style="font-size:11px;color:#9ca3af;">📍 <?php echo esc_html($fprofile->location); ?></div>
                <?php endif; ?>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <a href="?view=messenger&chat_with=<?php echo $friend_id; ?>" class="stitch-btn-primary" style="padding:7px 14px;font-size:12px;border-radius:8px;text-decoration:none;">💬 Message</a>
                <a href="?view=profile&uid=<?php echo $friend_id; ?>" class="stitch-btn-outline" style="padding:7px 14px;font-size:12px;border-radius:8px;text-decoration:none;">View</a>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Discover More -->
    <div style="text-align:center;padding:24px;background:linear-gradient(135deg,#f5f3ff,#f3e8ff);border-radius:12px;">
        <h4 style="margin:0 0 8px;font-size:16px;color:#1a1a2e;">Grow your network</h4>
        <p style="color:#6b7280;margin:0 0 16px;font-size:14px;">Find and connect with more MTTS alumni and ministers.</p>
        <a href="?view=directory" class="stitch-btn-primary">Browse Alumni Directory</a>
    </div>
</div>
