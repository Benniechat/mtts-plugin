<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$groups = \MttsLms\Models\Group::get_all_groups();
$my_groups = array();
if ( is_user_logged_in() ) {
    global $wpdb;
    $member_table = $wpdb->prefix . 'mtts_group_members';
    $my_group_ids = $wpdb->get_col( $wpdb->prepare( "SELECT group_id FROM $member_table WHERE user_id = %d", $user->ID ) );
}

$active_group_id = isset( $_GET['group_id'] ) ? intval( $_GET['group_id'] ) : null;
?>

<div class="mtts-dashboard-section" style="max-width:1200px; margin:0 auto;">
    
    <?php if ( $active_group_id ) : 
        $group = \MttsLms\Models\Group::get_group_with_creator( $active_group_id );
        $is_member = \MttsLms\Models\GroupMember::is_member( $active_group_id, $user->ID );
        $members = \MttsLms\Models\GroupMember::get_group_members( $active_group_id );
    ?>
        <!-- Group Detail View -->
        <div class="koinonia-glass" style="padding:40px; border-radius:15px; margin-bottom:30px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <h2 class="spiritual-gradient-text" style="margin:0; font-size:32px;"><?php echo esc_html( $group->name ); ?></h2>
                    <p style="color:#64748b; font-size:16px; margin-top:10px;"><?php echo esc_html( $group->description ); ?></p>
                    <div style="display:flex; gap:15px; margin-top:20px; font-size:13px; color:#94a3b8;">
                        <span><span class="dashicons dashicons-admin-users"></span> <?php echo count($members); ?> Ministers</span>
                        <span><span class="dashicons dashicons-lock"></span> <?php echo ucfirst($group->privacy); ?> Group</span>
                        <span><span class="dashicons dashicons-admin-site"></span> Created by <?php echo esc_html($group->creator_name); ?></span>
                    </div>
                </div>
                <div>
                    <?php if ( ! $is_member ) : ?>
                        <form method="post" action="">
                            <?php wp_nonce_field( 'mtts_alumni_social' ); ?>
                            <input type="hidden" name="mtts_alumni_action" value="join_group">
                            <input type="hidden" name="group_id" value="<?php echo $active_group_id; ?>">
                            <button type="submit" class="mtts-btn mtts-btn-primary" style="padding:10px 30px;">Join Koinonia</button>
                        </form>
                    <?php else : ?>
                        <span class="mtts-btn" style="background:#f1f5f9; color:#64748b; cursor:default;">Member</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:2fr 1fr; gap:30px;">
            <!-- Group Forum -->
            <div class="koinonia-glass" style="padding:30px; border-radius:15px;">
                <h3 style="margin-top:0;">Ministry Discussions</h3>
                <?php 
                $forum_posts = \MttsLms\Models\ForumPost::get_all( array( 'group_id' => $active_group_id ) );
                if ( empty( $forum_posts ) ) : ?>
                    <div style="text-align:center; padding:40px; color:#94a3b8;">
                        <p>No discussions yet. Be the first to propagate a word!</p>
                    </div>
                <?php else : foreach ( $forum_posts as $post ) : ?>
                    <div style="padding:15px; border-bottom:1px solid #f1f5f9;">
                        <h4 style="margin:0;"><a href="#" style="text-decoration:none; color:#1e293b;"><?php echo esc_html($post->title); ?></a></h4>
                        <div style="font-size:12px; color:#94a3b8; margin-top:5px;">
                            By <?php echo esc_html(get_the_author_meta('display_name', $post->author_id)); ?> ● <?php echo human_time_diff(strtotime($post->created_at)); ?> ago
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>

            <!-- Group Members -->
            <div class="koinonia-glass" style="padding:25px; border-radius:15px;">
                <h3 style="margin-top:0;">Ministers</h3>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ( $members as $member ) : ?>
                        <div style="display:flex; align-items:center;">
                            <img src="<?php echo get_avatar_url( $member->user_id ); ?>" style="width:35px; height:35px; border-radius:50%; margin-right:10px;" alt="">
                            <div>
                                <div style="font-size:13px; font-weight:700;"><?php echo esc_html($member->display_name); ?></div>
                                <div style="font-size:10px; color:#7c3aed;"><?php echo ucfirst($member->role); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    <?php else : ?>
        <!-- Discovery View -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;">
            <div>
                <h2 class="spiritual-gradient-text" style="margin:0; font-size:28px;">Ministry Circles</h2>
                <p style="color:#64748b; margin:5px 0 0;">Find your covenant community for study and ministry.</p>
            </div>
            <button onclick="document.getElementById('create-group-modal').style.display='flex'" class="mtts-btn mtts-btn-primary" style="padding:10px 25px;">Create Circle</button>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:25px;">
            <?php foreach ( $groups as $group ) : 
                $is_my_group = in_array( $group->id, $my_group_ids );
            ?>
                <div class="koinonia-glass mtts-social-card" style="padding:25px; transition:transform 0.2s; cursor:pointer;" onclick="window.location.href='?view=groups&group_id=<?php echo $group->id; ?>'">
                    <?php if ( $is_my_group ) : ?>
                         <div style="position:absolute; top:20px; right:20px; background:#f5f3ff; color:#7c3aed; font-size:10px; font-weight:800; padding:2px 8px; border-radius:10px;">MY KOINONIA</div>
                    <?php endif; ?>
                    <h3 style="margin:0; font-size:20px; color:#1e293b;"><?php echo esc_html( $group->name ); ?></h3>
                    <p style="font-size:13px; color:#64748b; height:40px; overflow:hidden; margin:10px 0 20px;"><?php echo esc_html( $group->description ); ?></p>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; color:#94a3b8;"><span class="dashicons dashicons-groups" style="font-size:16px;"></span> Discovery level</span>
                        <a href="?view=groups&group_id=<?php echo $group->id; ?>" class="mtts-btn mtts-btn-sm" style="border-radius:20px; padding:5px 15px;">Enter</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Create Group Modal (Basic implementation) -->
        <div id="create-group-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; backdrop-filter:blur(5px);">
            <div class="koinonia-glass" style="width:500px; padding:40px; border-radius:20px; background:#fff;">
                <h3 class="spiritual-gradient-text" style="font-size:24px; margin-top:0;">Establish a Ministry Circle</h3>
                <form method="post" action="">
                    <?php wp_nonce_field( 'mtts_alumni_social' ); ?>
                    <input type="hidden" name="mtts_alumni_action" value="create_group">
                    
                    <div style="margin-bottom:15px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px;">CIRCLE NAME</label>
                        <input type="text" name="name" required style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
                    </div>
                    
                    <div style="margin-bottom:15px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px;">MISSION STATEMENT</label>
                        <textarea name="description" required style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px; height:80px;"></textarea>
                    </div>

                    <div style="margin-bottom:25px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px;">PRIVACY</label>
                        <select name="privacy" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
                            <option value="public">Public - Join freely</option>
                            <option value="private">Private - Ministerial vetting</option>
                        </select>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <button type="button" onclick="document.getElementById('create-group-modal').style.display='none'" class="mtts-btn" style="background:#f1f5f9; color:#64748b;">Sanctify & Cancel</button>
                        <button type="submit" class="mtts-btn mtts-btn-primary">Establish</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
