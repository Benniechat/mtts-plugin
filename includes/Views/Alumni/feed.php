<?php 
/**
 * Alumni Community Feed - Stitch UI Design
 */
$u = wp_get_current_user();
$my_profile = \MttsLms\Models\AlumniProfile::get_by_user( $u->ID );
$avatar_url  = $my_profile->profile_picture_url ?: get_avatar_url( $u->ID );

// Suggested connections (non-friends)
$all_alumni = get_users(['role' => 'mtts_alumni', 'exclude' => [$u->ID], 'number' => 4]);
?>
<style>
:root {
    --stitch-blue: #0d27a5;
    --stitch-blue-light: #e8ecfd;
    --stitch-blue-mid: #3b5bdb;
    --stitch-text: #1a1a2e;
    --stitch-muted: #6b7280;
    --stitch-border: #e5e7eb;
    --stitch-bg: #f3f4f8;
    --stitch-white: #ffffff;
    --stitch-card-radius: 12px;
    --stitch-shadow: 0 1px 4px rgba(13,39,165,0.08);
}
.stitch-feed-layout {
    display: grid;
    grid-template-columns: 280px 1fr 300px;
    gap: 24px;
    max-width: 1200px;
    margin: 0 auto;
    align-items: start;
}
.stitch-card {
    background: var(--stitch-white);
    border-radius: var(--stitch-card-radius);
    box-shadow: var(--stitch-shadow);
    border: 1px solid var(--stitch-border);
    overflow: hidden;
    margin-bottom: 16px;
}
.stitch-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 8px;
    text-decoration: none;
    color: var(--stitch-text);
    font-weight: 500;
    font-size: 14px;
    transition: background 0.15s;
}
.stitch-nav-item:hover, .stitch-nav-item.active {
    background: var(--stitch-blue-light);
    color: var(--stitch-blue);
    text-decoration: none;
}
.stitch-nav-item .nav-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}
.stitch-btn-primary {
    background: var(--stitch-blue);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 18px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.stitch-btn-primary:hover { background: var(--stitch-blue-mid); color: #fff; text-decoration: none; }
.stitch-btn-outline {
    background: transparent;
    color: var(--stitch-blue);
    border: 1.5px solid var(--stitch-blue);
    border-radius: 8px;
    padding: 7px 16px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.stitch-btn-outline:hover { background: var(--stitch-blue-light); text-decoration: none; }
.stitch-section-title {
    font-weight: 700;
    font-size: 15px;
    color: var(--stitch-text);
    padding: 16px 20px 8px;
    border-bottom: 1px solid var(--stitch-border);
    margin-bottom: 12px;
}
.post-action-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: var(--stitch-muted);
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    flex: 1;
    justify-content: center;
    transition: background 0.15s;
}
.post-action-btn:hover { background: var(--stitch-bg); }
.post-action-btn.amen-active { color: #e53e3e; }
.feed-author-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
.stitch-tag {
    display: inline-block;
    background: var(--stitch-blue-light);
    color: var(--stitch-blue);
    border-radius: 100px;
    padding: 3px 10px;
    font-size: 12px;
    font-weight: 600;
    margin: 2px;
}
.suggestion-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
}
.suggestion-card img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
@media (max-width: 1100px) {
    .stitch-feed-layout { grid-template-columns: 240px 1fr; }
    .stitch-right-panel { display: none; }
}
@media (max-width: 768px) {
    .stitch-feed-layout { grid-template-columns: 1fr; }
    .stitch-left-panel { display: none; }
}
</style>

<div class="stitch-feed-layout">
    <!-- LEFT PANEL: Navigation -->
    <div class="stitch-left-panel">
        <div class="stitch-card" style="padding: 16px;">
            <!-- User Quick Profile -->
            <a href="?view=profile" class="stitch-nav-item" style="padding: 10px; margin-bottom: 8px; border-bottom: 1px solid var(--stitch-border); padding-bottom: 16px;">
                <img src="<?php echo esc_url($avatar_url); ?>" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid var(--stitch-blue);">
                <div>
                    <div style="font-weight:700;font-size:14px;color:var(--stitch-text);"><?php echo esc_html($u->display_name); ?></div>
                    <div style="font-size:12px;color:var(--stitch-blue);font-weight:600;"><?php echo esc_html(substr($my_profile->headline ?: 'MTTS Alumni', 0, 32)); ?></div>
                </div>
            </a>

            <nav style="padding-top:8px;">
                <a href="?view=feed" class="stitch-nav-item <?php echo (!isset($_GET['view']) || $_GET['view'] === 'feed') ? 'active' : ''; ?>">
                    <span class="nav-icon" style="background:#e8ecfd;"><span class="dashicons dashicons-admin-home" style="color:var(--stitch-blue);"></span></span>
                    Home Feed
                </a>
                <a href="?view=events" class="stitch-nav-item <?php echo (isset($_GET['view']) && $_GET['view'] === 'events') ? 'active' : ''; ?>">
                    <span class="nav-icon" style="background:#fceef3;"><span class="dashicons dashicons-calendar-alt" style="color:#e53e3e;"></span></span>
                    Seminary Events
                </a>
                <a href="?view=groups" class="stitch-nav-item <?php echo (isset($_GET['view']) && $_GET['view'] === 'groups') ? 'active' : ''; ?>">
                    <span class="nav-icon" style="background:#e6f4ea;"><span class="dashicons dashicons-groups" style="color:#2e7d32;"></span></span>
                    Research Groups
                </a>
                <a href="?view=friends" class="stitch-nav-item <?php echo (isset($_GET['view']) && $_GET['view'] === 'friends') ? 'active' : ''; ?>">
                    <span class="nav-icon" style="background:#fff3e0;"><span class="dashicons dashicons-buddicons-buddybar" style="color:#e65c00;"></span></span>
                    My Connections
                </a>
                <a href="?view=directory" class="stitch-nav-item <?php echo (isset($_GET['view']) && $_GET['view'] === 'directory') ? 'active' : ''; ?>">
                    <span class="nav-icon" style="background:#f3e8ff;"><span class="dashicons dashicons-id-alt" style="color:#7c3aed;"></span></span>
                    Alumni Directory
                </a>
                <a href="?view=jobs" class="stitch-nav-item <?php echo (isset($_GET['view']) && $_GET['view'] === 'jobs') ? 'active' : ''; ?>">
                    <span class="nav-icon" style="background:#e8f5e9;"><span class="dashicons dashicons-businessman" style="color:#388e3c;"></span></span>
                    Alumni Resources
                </a>
                <a href="?view=messenger" class="stitch-nav-item <?php echo (isset($_GET['view']) && $_GET['view'] === 'messenger') ? 'active' : ''; ?>">
                    <span class="nav-icon" style="background:#e8ecfd;"><span class="dashicons dashicons-email-alt" style="color:var(--stitch-blue);"></span></span>
                    Messages
                </a>
                <a href="?view=profile-edit" class="stitch-nav-item">
                    <span class="nav-icon" style="background:#f3f4f8;"><span class="dashicons dashicons-edit-page" style="color:var(--stitch-muted);"></span></span>
                    Edit Profile
                </a>
            </nav>

            <div style="border-top:1px solid var(--stitch-border);margin:12px 0;"></div>

            <!-- Recent Groups -->
            <div style="padding:0 4px;">
                <div style="font-size:13px;font-weight:700;color:var(--stitch-muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Recent Groups</div>
                <?php
                $rcnt_groups = \MttsLms\Models\Group::get_all_groups();
                $rcnt_groups = array_slice((array)$rcnt_groups, 0, 3);
                foreach ($rcnt_groups as $rg):
                ?>
                <a href="?view=groups&group_id=<?php echo $rg->id; ?>" class="stitch-nav-item" style="padding:6px 8px;font-size:13px;">
                    <span style="font-size:15px;">👥</span> <?php echo esc_html($rg->name); ?>
                </a>
                <?php endforeach; ?>
                <?php if (empty($rcnt_groups)): ?>
                <a href="?view=groups" class="stitch-nav-item" style="padding:6px 8px;font-size:13px;">
                    <span style="font-size:15px;">📌</span> Ethics Symposium
                </a>
                <a href="?view=groups" class="stitch-nav-item" style="padding:6px 8px;font-size:13px;">
                    <span style="font-size:15px;">📌</span> Patristics Circle
                </a>
                <?php endif; ?>
            </div>

            <div style="border-top:1px solid var(--stitch-border);margin:12px 0;"></div>
            <a href="<?php echo wp_logout_url(home_url()); ?>" class="stitch-nav-item" style="color:#e53e3e;">
                <span class="nav-icon" style="background:#fff5f5;"><span class="dashicons dashicons-exit" style="color:#e53e3e;"></span></span>
                Log Out
            </a>
        </div>
    </div>

    <!-- CENTER: Feed -->
    <div class="stitch-center-feed">
        <!-- Post Composer -->
        <div class="stitch-card" style="padding:20px;margin-bottom:16px;">
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field('mtts_alumni_social'); ?>
                <input type="hidden" name="mtts_alumni_action" value="create_post">
                <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;">
                    <img src="<?php echo esc_url($avatar_url); ?>" class="feed-author-avatar">
                    <button type="button" onclick="stExpand()" style="flex:1;text-align:left;background:var(--stitch-bg);border:1.5px solid var(--stitch-border);border-radius:24px;padding:11px 18px;font-size:14px;color:var(--stitch-muted);cursor:pointer;font-family:inherit;">
                        What's on your mind, <?php echo esc_html($u->first_name ?: $u->display_name); ?>?
                    </button>
                </div>
                <div id="stitch-post-expand" style="display:none;">
                    <textarea name="content" rows="4" id="stitch-post-textarea" placeholder="Share your testimony, ministry update, or a prayer point..." style="width:100%;border:1.5px solid var(--stitch-border);border-radius:8px;padding:12px;font-size:15px;resize:none;outline:none;font-family:inherit;color:var(--stitch-text);"></textarea>
                    <div id="stitch-media-preview" style="margin-top:10px;display:none;position:relative;">
                        <img id="stitch-prev-img" style="width:100%;border-radius:8px;max-height:300px;object-fit:cover;display:none;">
                        <video id="stitch-prev-vid" style="width:100%;border-radius:8px;display:none;" controls></video>
                        <button type="button" onclick="stClearMedia()" style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.6);color:#fff;border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">&times;</button>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;">
                        <div style="display:flex;gap:8px;">
                            <label style="display:flex;align-items:center;gap:6px;padding:7px 12px;border-radius:6px;cursor:pointer;background:var(--stitch-bg);font-size:13px;font-weight:600;color:var(--stitch-muted);">
                                <span class="dashicons dashicons-format-image" style="color:#2e7d32;"></span> Photo/Video
                                <input type="file" name="media_file" accept="image/*,video/*" style="display:none;" onchange="stPreviewMedia(this)">
                            </label>
                        </div>
                        <button type="submit" class="stitch-btn-primary">Share Post</button>
                    </div>
                </div>
                <div style="display:flex;border-top:1px solid var(--stitch-border);padding-top:10px;gap:4px;" id="stitch-post-actions-bar">
                    <button type="button" class="post-action-btn" onclick="stExpand()">
                        <span class="dashicons dashicons-video-alt3" style="color:#e53e3e;"></span> Live Video
                    </button>
                    <label class="post-action-btn" style="cursor:pointer;">
                        <span class="dashicons dashicons-format-image" style="color:#2e7d32;"></span> Photo
                        <input type="file" name="media_file" accept="image/*,video/*" style="display:none;" onchange="stPreviewMedia(this);stExpand();">
                    </label>
                    <button type="button" class="post-action-btn" onclick="stExpand()">
                        <span class="dashicons dashicons-smiley" style="color:#f59e0b;"></span> Activity
                    </button>
                </div>
            </form>
        </div>

        <!-- Feed Posts -->
        <?php foreach ( $posts as $post ) :
            $post_profile = \MttsLms\Models\AlumniProfile::get_by_user($post->author_id);
            $post_avatar = $post_profile->profile_picture_url ?: get_avatar_url($post->author_id);
        ?>
        <div class="stitch-card" style="margin-bottom:16px;">
            <div style="padding:16px 20px;">
                <!-- Post Header -->
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <div style="display:flex;gap:12px;align-items:center;">
                        <a href="?view=profile&uid=<?php echo $post->author_id; ?>">
                            <img src="<?php echo esc_url($post_avatar); ?>" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid var(--stitch-blue-light);">
                        </a>
                        <div>
                            <a href="?view=profile&uid=<?php echo $post->author_id; ?>" style="font-weight:700;font-size:14px;color:var(--stitch-text);text-decoration:none;"><?php echo esc_html($post->display_name); ?></a>
                            <div style="font-size:12px;color:var(--stitch-muted);">
                                <?php 
                                $pp = \MttsLms\Models\AlumniProfile::get_by_user($post->author_id);
                                echo esc_html($pp->headline ?: 'MTTS Alumni'); 
                                ?> • <?php echo human_time_diff(strtotime($post->created_at), current_time('timestamp')); ?> ago
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div style="font-size:15px;line-height:1.7;color:var(--stitch-text);margin-bottom:12px;">
                    <?php echo nl2br(esc_html($post->content)); ?>
                </div>

                <!-- Media -->
                <?php if ($post->media_url) : ?>
                <div style="border-radius:8px;overflow:hidden;margin-bottom:12px;border:1px solid var(--stitch-border);">
                    <?php if ($post->media_type === 'video') : ?>
                        <video style="width:100%;max-height:400px;object-fit:cover;" controls>
                            <source src="<?php echo esc_url($post->media_url); ?>">
                        </video>
                    <?php else : ?>
                        <img src="<?php echo esc_url($post->media_url); ?>" style="width:100%;max-height:500px;object-fit:cover;">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Like count -->
                <?php if ($post->likes_count > 0) : ?>
                <div style="font-size:13px;color:var(--stitch-muted);border-bottom:1px solid var(--stitch-border);padding-bottom:10px;margin-bottom:6px;">
                    <span style="color:#e53e3e;">❤</span> <?php echo $post->likes_count; ?> Amen<?php echo $post->likes_count > 1 ? 's' : ''; ?>
                    <?php if ($post->comments_count > 0) : ?>
                        <span style="float:right;"><?php echo $post->comments_count; ?> comment<?php echo $post->comments_count > 1 ? 's' : ''; ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div style="display:flex;border-top:1px solid var(--stitch-border);padding-top:6px;gap:4px;">
                    <button type="button" class="post-action-btn <?php echo $post->likes_count > 0 ? 'amen-active' : ''; ?>" onclick="stSocialPost('amen_post', <?php echo $post->id; ?>, this)">
                        <span class="dashicons dashicons-heart"></span> Amen
                    </button>
                    <button type="button" class="post-action-btn" onclick="stToggleComments(<?php echo $post->id; ?>)">
                        <span class="dashicons dashicons-admin-comments"></span> Comment
                    </button>
                    <button type="button" class="post-action-btn" onclick="stSocialPost('propagate_post', <?php echo $post->id; ?>, this)">
                        <span class="dashicons dashicons-share"></span> Share
                    </button>
                </div>

                <!-- Comments -->
                <div id="st-comments-<?php echo $post->id; ?>" style="display:none;margin-top:12px;border-top:1px solid var(--stitch-border);padding-top:12px;">
                    <?php
                    $comments = \MttsLms\Models\AlumniComment::get_by_post($post->id);
                    foreach ((array)$comments as $cmt):
                        $c_avatar = get_avatar_url($cmt->author_id);
                    ?>
                    <div style="display:flex;gap:10px;margin-bottom:10px;">
                        <img src="<?php echo $c_avatar; ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                        <div style="background:var(--stitch-bg);border-radius:16px;padding:8px 14px;flex:1;">
                            <div style="font-weight:700;font-size:13px;color:var(--stitch-text);"><?php echo esc_html(get_userdata($cmt->author_id)->display_name); ?></div>
                            <div style="font-size:13px;color:var(--stitch-text);margin-top:2px;"><?php echo esc_html($cmt->content); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <!-- Add comment -->
                    <form method="post" action="" style="display:flex;gap:10px;align-items:center;margin-top:8px;">
                        <?php wp_nonce_field('mtts_alumni_social'); ?>
                        <input type="hidden" name="mtts_alumni_action" value="add_comment">
                        <input type="hidden" name="post_id" value="<?php echo $post->id; ?>">
                        <img src="<?php echo esc_url($avatar_url); ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                        <input type="text" name="content" placeholder="Write a comment..." required style="flex:1;border:1.5px solid var(--stitch-border);border-radius:20px;padding:8px 16px;font-size:13px;outline:none;background:var(--stitch-bg);">
                        <button type="submit" class="stitch-btn-primary" style="padding:8px 14px;border-radius:20px;">Post</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($posts)): ?>
        <div class="stitch-card" style="padding:60px;text-align:center;">
            <div style="font-size:48px;margin-bottom:16px;">✍️</div>
            <h3 style="color:var(--stitch-text);margin:0 0 8px;">No posts yet</h3>
            <p style="color:var(--stitch-muted);margin:0;">Be the first to share something with the community!</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT PANEL: Suggestions & Events -->
    <div class="stitch-right-panel">
        <!-- Suggested Connections -->
        <div class="stitch-card" style="margin-bottom:16px;">
            <div class="stitch-section-title">Suggested Connections</div>
            <?php foreach ($all_alumni as $alum_s):
                $alum_p = \MttsLms\Models\AlumniProfile::get_by_user($alum_s->ID);
                $existing_req = \MttsLms\Models\FriendRequest::get_request($u->ID, $alum_s->ID);
            ?>
            <div class="suggestion-card">
                <img src="<?php echo esc_url($alum_p->profile_picture_url ?: get_avatar_url($alum_s->ID)); ?>" alt="">
                <div style="flex:1;min-width:0;">
                    <a href="?view=profile&uid=<?php echo $alum_s->ID; ?>" style="font-weight:700;font-size:13px;color:var(--stitch-text);text-decoration:none;display:block;"><?php echo esc_html($alum_s->display_name); ?></a>
                    <div style="font-size:11px;color:var(--stitch-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo esc_html(substr($alum_p->headline ?: 'MTTS Alumni', 0, 28)); ?></div>
                </div>
                <?php if (!$existing_req): ?>
                <button type="button" class="stitch-btn-outline" style="padding:5px 10px;font-size:11px;border-radius:100px;" onclick="stConnect(<?php echo $alum_s->ID; ?>, this)">Connect</button>
                <?php elseif ($existing_req->status === 'accepted'): ?>
                <a href="?view=messenger&chat_with=<?php echo $alum_s->ID; ?>" class="stitch-btn-outline" style="padding:5px 10px;font-size:11px;border-radius:100px;">Chat</a>
                <?php else: ?>
                <span style="font-size:11px;color:var(--stitch-muted);font-style:italic;">Pending</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <div style="padding:10px 20px;border-top:1px solid var(--stitch-border);">
                <a href="?view=directory" style="font-size:13px;font-weight:600;color:var(--stitch-blue);text-decoration:none;">View more →</a>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="stitch-card">
            <div class="stitch-section-title">Upcoming Events</div>
            <?php
            $ev_list = [
                ['title' => 'Annual Alumni Banquet 2024', 'loc' => 'Main Campus', 'icon' => '🏛'],
                ['title' => 'Global Missions Webinar', 'loc' => 'Virtual Event', 'icon' => '📹'],
            ];
            foreach ($ev_list as $ev):
            ?>
            <div style="padding:12px 20px;border-bottom:1px solid var(--stitch-border);display:flex;gap:12px;align-items:flex-start;">
                <div style="font-size:22px;"><?php echo $ev['icon']; ?></div>
                <div>
                    <div style="font-weight:600;font-size:13px;color:var(--stitch-text);margin-bottom:3px;"><?php echo esc_html($ev['title']); ?></div>
                    <div style="font-size:12px;color:var(--stitch-muted);">📍 <?php echo esc_html($ev['loc']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            <div style="padding:10px 20px;">
                <a href="?view=events" style="font-size:13px;font-weight:600;color:var(--stitch-blue);text-decoration:none;">View all events →</a>
            </div>
        </div>

        <!-- Footer links -->
        <div style="padding:16px;font-size:11px;color:var(--stitch-muted);line-height:2;">
            <a href="#" style="color:var(--stitch-muted);text-decoration:none;margin-right:8px;">About</a>
            <a href="#" style="color:var(--stitch-muted);text-decoration:none;margin-right:8px;">Privacy</a>
            <a href="#" style="color:var(--stitch-muted);text-decoration:none;margin-right:8px;">Help</a>
            <a href="#" style="color:var(--stitch-muted);text-decoration:none;">Seminary Terms</a><br>
            MTTS Alumni © <?php echo date('Y'); ?>
        </div>
    </div>
</div>

<script>
function stExpand() {
    document.getElementById('stitch-post-expand').style.display = 'block';
    document.getElementById('stitch-post-actions-bar').style.display = 'none';
    document.getElementById('stitch-post-textarea').focus();
}

function stPreviewMedia(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const preview = document.getElementById('stitch-media-preview');
    const img = document.getElementById('stitch-prev-img');
    const vid = document.getElementById('stitch-prev-vid');
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.style.display = 'block';
        if (file.type.startsWith('video')) {
            img.style.display = 'none';
            vid.style.display = 'block';
            vid.src = e.target.result;
        } else {
            vid.style.display = 'none';
            img.style.display = 'block';
            img.src = e.target.result;
        }
    };
    reader.readAsDataURL(file);
}

function stClearMedia() {
    document.querySelector('input[name="media_file"]').value = '';
    document.getElementById('stitch-media-preview').style.display = 'none';
}

function stSocialPost(action, id, btn) {
    const fd = new FormData();
    fd.append('mtts_alumni_action', action);
    fd.append('post_id', id);
    fd.append('_wpnonce', '<?php echo wp_create_nonce("mtts_alumni_social"); ?>');
    btn.disabled = true;
    fetch('', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if(d.success) location.reload(); })
    .catch(() => { btn.disabled = false; });
}

function stConnect(uid, btn) {
    const fd = new FormData();
    fd.append('mtts_alumni_action', 'send_friend_request');
    fd.append('receiver_id', uid);
    fd.append('_wpnonce', '<?php echo wp_create_nonce("mtts_alumni_social"); ?>');
    btn.disabled = true;
    btn.textContent = 'Sent!';
    fetch('', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if(d.success) btn.style.color = '#6b7280'; })
    .catch(() => { btn.disabled = false; btn.textContent = 'Connect'; });
}

function stToggleComments(id) {
    const el = document.getElementById('st-comments-' + id);
    if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
