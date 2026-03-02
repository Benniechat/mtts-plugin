<?php
/**
 * Ministry Circles (Groups) – Stitch UI Design
 */
if (!defined('ABSPATH')) exit;

$groups = \MttsLms\Models\Group::get_all_groups();
$my_group_ids = [];
if (is_user_logged_in()) {
    global $wpdb;
    $member_table = $wpdb->prefix . 'mtts_group_members';
    $my_group_ids = $wpdb->get_col($wpdb->prepare("SELECT group_id FROM $member_table WHERE user_id = %d", $user->ID));
}

$active_group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : null;
?>

<style>
.st-groups-layout {
    max-width: 1200px;
    margin: 0 auto;
}
.st-group-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(13,39,165,.08);
    overflow: hidden;
    transition: box-shadow 0.2s, transform 0.2s;
    cursor: pointer;
    position: relative;
}
.st-group-card:hover {
    box-shadow: 0 6px 24px rgba(13,39,165,.14);
    transform: translateY(-3px);
}
.st-group-card-banner {
    height: 80px;
    background: linear-gradient(135deg, #0d27a5, #7c3aed);
    position: relative;
}
.st-group-avatar {
    width: 60px; height: 60px;
    border-radius: 12px;
    background: #fff;
    border: 3px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    position: absolute;
    bottom: -30px;
    left: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}
.st-group-body { padding: 40px 20px 20px; }
.st-group-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}
.st-group-detail-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}
.st-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(13,39,165,.08);
    overflow: hidden;
    margin-bottom: 16px;
}
@media(max-width:768px) { .st-group-detail-layout { grid-template-columns: 1fr; } }
</style>

<div class="st-groups-layout">
    <?php if ($active_group_id):
        $group = \MttsLms\Models\Group::get_group_with_creator($active_group_id);
        $is_member = \MttsLms\Models\GroupMember::is_member($active_group_id, $user->ID);
        $members = \MttsLms\Models\GroupMember::get_group_members($active_group_id);
    ?>
    <!-- GROUP DETAIL VIEW -->
    <a href="?view=groups" style="display:inline-flex;align-items:center;gap:8px;color:#0d27a5;text-decoration:none;font-weight:600;font-size:14px;margin-bottom:20px;">
        ← Back to Ministry Circles
    </a>

    <div class="st-card" style="overflow:visible;margin-bottom:24px;">
        <div style="height:160px;background:linear-gradient(135deg,#0d27a5,#7c3aed);border-radius:12px 12px 0 0;"></div>
        <div style="padding:24px 32px;border-radius:0 0 12px 12px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;">
                <div>
                    <h2 style="margin:0 0 6px;font-size:24px;font-weight:800;color:#1a1a2e;"><?php echo esc_html($group->name); ?></h2>
                    <p style="color:#6b7280;font-size:14px;margin:0 0 12px;line-height:1.6;"><?php echo esc_html($group->description); ?></p>
                    <div style="display:flex;gap:20px;font-size:13px;color:#9ca3af;flex-wrap:wrap;">
                        <span>👥 <?php echo count($members); ?> Ministers</span>
                        <span>🔒 <?php echo ucfirst($group->privacy); ?> Group</span>
                        <span>👤 Created by <?php echo esc_html($group->creator_name); ?></span>
                    </div>
                </div>
                <?php if (!$is_member): ?>
                <form method="post" action="">
                    <?php wp_nonce_field('mtts_alumni_social'); ?>
                    <input type="hidden" name="mtts_alumni_action" value="join_group">
                    <input type="hidden" name="group_id" value="<?php echo $active_group_id; ?>">
                    <button type="submit" class="stitch-btn-primary" style="padding:10px 28px;font-size:14px;border-radius:8px;">🤝 Join Circle</button>
                </form>
                <?php else: ?>
                <span class="stitch-btn-outline" style="cursor:default;border-color:#2e7d32;color:#2e7d32;padding:10px 24px;">✓ Member</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="st-group-detail-layout">
        <!-- Discussions -->
        <div class="st-card">
            <div style="padding:18px 24px;border-bottom:1px solid #f3f4f8;display:flex;justify-content:space-between;align-items:center;">
                <h3 style="margin:0;font-size:17px;font-weight:700;color:#1a1a2e;">💬 Ministry Discussions</h3>
                <?php if ($is_member): ?>
                <button onclick="document.getElementById('st-new-post-form').style.display='block';" class="stitch-btn-primary" style="font-size:12px;padding:6px 14px;border-radius:8px;">+ Start Discussion</button>
                <?php endif; ?>
            </div>

            <?php if ($is_member): ?>
            <div id="st-new-post-form" style="display:none;padding:16px 24px;border-bottom:1px solid #f3f4f8;background:#f9fafb;">
                <form method="post" action="">
                    <?php wp_nonce_field('mtts_alumni_social'); ?>
                    <input type="hidden" name="mtts_alumni_action" value="create_post">
                    <input type="hidden" name="type" value="group">
                    <textarea name="content" rows="3" placeholder="Share a word, prayer point, or resource..." style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:10px;font-size:14px;resize:none;font-family:inherit;"></textarea>
                    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px;">
                        <button type="button" onclick="document.getElementById('st-new-post-form').style.display='none'" class="stitch-btn-outline" style="font-size:13px;padding:6px 14px;border-radius:8px;">Cancel</button>
                        <button type="submit" class="stitch-btn-primary" style="font-size:13px;padding:6px 14px;border-radius:8px;">Post</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <?php
            $forum_posts = \MttsLms\Models\ForumPost::get_all(['group_id' => $active_group_id]);
            if (empty($forum_posts)): ?>
            <div style="text-align:center;padding:60px 20px;color:#9ca3af;">
                <div style="font-size:40px;margin-bottom:12px;">📢</div>
                <p style="margin:0;">No discussions yet. Be the first to share a word!</p>
            </div>
            <?php else: ?>
            <?php foreach ($forum_posts as $fp): ?>
            <div style="padding:16px 24px;border-bottom:1px solid #f3f4f8;">
                <div style="display:flex;gap:10px;">
                    <img src="<?php echo get_avatar_url($fp->author_id); ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    <div>
                        <div style="font-weight:700;font-size:14px;color:#1a1a2e;"><?php echo esc_html(get_the_author_meta('display_name', $fp->author_id)); ?></div>
                        <div style="font-size:12px;color:#9ca3af;"><?php echo human_time_diff(strtotime($fp->created_at)); ?> ago</div>
                        <h4 style="margin:8px 0;font-size:15px;color:#1a1a2e;"><?php echo esc_html($fp->title); ?></h4>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <!-- Members -->
        <div>
            <div class="st-card">
                <div style="padding:16px 20px;border-bottom:1px solid #f3f4f8;">
                    <h4 style="margin:0;font-size:15px;font-weight:700;color:#1a1a2e;">👥 Ministers (<?php echo count($members); ?>)</h4>
                </div>
                <?php foreach ($members as $mem): ?>
                <div style="padding:12px 20px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #f3f4f8;">
                    <img src="<?php echo get_avatar_url($mem->user_id); ?>" style="width:38px;height:38px;border-radius:50%;object-fit:cover;">
                    <div style="flex:1;min-width:0;">
                        <a href="?view=profile&uid=<?php echo $mem->user_id; ?>" style="font-weight:700;font-size:13px;color:#1a1a2e;text-decoration:none;"><?php echo esc_html($mem->display_name); ?></a>
                        <div style="font-size:11px;color:#0d27a5;font-weight:600;"><?php echo ucfirst($mem->role); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- GROUPS DISCOVERY VIEW -->
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;margin-bottom:28px;">
        <div>
            <h2 style="margin:0;font-size:26px;font-weight:800;color:#1a1a2e;">Ministry Circles</h2>
            <p style="color:#6b7280;margin:5px 0 0;font-size:14px;">Find your covenant community for study and ministry.</p>
        </div>
        <button onclick="document.getElementById('st-create-group-modal').style.display='flex'" class="stitch-btn-primary" style="padding:10px 24px;border-radius:8px;">
            + Create Circle
        </button>
    </div>

    <?php if (empty($groups)): ?>
    <div style="text-align:center;padding:80px;background:#fff;border-radius:12px;border:1px solid #e5e7eb;">
        <div style="font-size:48px;margin-bottom:16px;">⛪</div>
        <h3 style="color:#1a1a2e;margin:0 0 8px;">No circles yet</h3>
        <p style="color:#6b7280;margin:0 0 20px;">Be the first to start a ministry community.</p>
        <button onclick="document.getElementById('st-create-group-modal').style.display='flex'" class="stitch-btn-primary">Create First Circle</button>
    </div>
    <?php else: ?>
    <div class="st-group-grid">
        <?php foreach ($groups as $group):
            $is_mine = in_array($group->id, (array)$my_group_ids);
            $emojis = ['📖', '⛪', '🕊', '✝', '🎓', '🙏', '📢', '🌍'];
            $emoji = $emojis[$group->id % count($emojis)];
        ?>
        <div class="st-group-card" onclick="window.location.href='?view=groups&group_id=<?php echo $group->id; ?>'">
            <div class="st-group-card-banner" style="background: linear-gradient(135deg, hsl(<?php echo ($group->id * 47 % 360); ?>deg, 70%, 35%), hsl(<?php echo (($group->id * 47 + 60) % 360); ?>deg, 60%, 50%));">
                <?php if ($is_mine): ?>
                <span style="position:absolute;top:10px;right:12px;background:rgba(255,255,255,.9);color:#0d27a5;font-size:10px;font-weight:800;padding:2px 10px;border-radius:100px;letter-spacing:.5px;">MY CIRCLE</span>
                <?php endif; ?>
                <div class="st-group-avatar"><?php echo $emoji; ?></div>
            </div>
            <div class="st-group-body">
                <h3 style="margin:0 0 8px;font-size:17px;font-weight:700;color:#1a1a2e;"><?php echo esc_html($group->name); ?></h3>
                <p style="font-size:13px;color:#6b7280;margin:0 0 16px;height:40px;overflow:hidden;line-height:1.6;"><?php echo esc_html($group->description); ?></p>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:12px;color:#9ca3af;">🔒 <?php echo ucfirst($group->privacy); ?></span>
                    <a href="?view=groups&group_id=<?php echo $group->id; ?>" class="stitch-btn-primary" style="font-size:12px;padding:6px 16px;border-radius:8px;text-decoration:none;" onclick="event.stopPropagation();">Enter →</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Create Group Modal -->
    <div id="st-create-group-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
        <div style="background:#fff;border-radius:16px;padding:36px;width:520px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.2);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <h3 style="margin:0;font-size:22px;font-weight:800;color:#1a1a2e;">⛪ Establish a Ministry Circle</h3>
                <button onclick="document.getElementById('st-create-group-modal').style.display='none'" style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            <form method="post" action="">
                <?php wp_nonce_field('mtts_alumni_social'); ?>
                <input type="hidden" name="mtts_alumni_action" value="create_group">
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;margin-bottom:6px;">Circle Name</label>
                    <input type="text" name="name" required placeholder="e.g. Biblical Hebrew Study Group" style="width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;font-family:inherit;box-sizing:border-box;">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;margin-bottom:6px;">Mission Statement</label>
                    <textarea name="description" required placeholder="Describe the purpose of this circle..." style="width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;height:90px;resize:none;font-family:inherit;box-sizing:border-box;"></textarea>
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;margin-bottom:6px;">Privacy</label>
                    <select name="privacy" style="width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;">
                        <option value="public">🌍 Public — Anyone can join</option>
                        <option value="private">🔒 Private — Ministerial vetting required</option>
                    </select>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;">
                    <button type="button" onclick="document.getElementById('st-create-group-modal').style.display='none'" class="stitch-btn-outline" style="border-radius:8px;padding:10px 20px;">Cancel</button>
                    <button type="submit" class="stitch-btn-primary" style="border-radius:8px;padding:10px 24px;">Establish Circle</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
