<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="mtts-dashboard-section" style="max-width:1000px; margin:0 auto;">
    <div style="margin-bottom:40px;">
        <h2 class="spiritual-gradient-text" style="margin:0; font-size:28px;">Fellowship Circle</h2>
        <p style="color:#64748b; margin:5px 0 0;">Managing your covenant connections and pending invitations.</p>
    </div>

    <?php if ( ! empty( $pending_requests ) ) : ?>
        <div class="koinonia-glass" style="padding:30px; border-radius:15px; margin-bottom:40px; border-left:4px solid #7c3aed;">
            <h3 style="margin-top:0; font-size:18px; color:#1e293b;"><span class="dashicons dashicons-id-alt" style="vertical-align:middle; margin-right:8px;"></span> Pending Invitations</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px; margin-top:20px;">
                <?php foreach ( $pending_requests as $req ) : 
                    $sender = get_userdata( $req->sender_id );
                    $sender_profile = \MttsLms\Models\AlumniProfile::get_by_user( $req->sender_id );
                ?>
                    <div class="mtts-social-card" style="padding:20px; background:#fff; display:flex; flex-direction:column; align-items:center; text-align:center;">
                        <img src="<?php echo get_avatar_url( $req->sender_id ); ?>" style="width:60px; height:60px; border-radius:50%; margin-bottom:10px;" alt="">
                        <div style="font-weight:700; color:#1e293b;"><?php echo esc_html( $sender->display_name ); ?></div>
                        <div style="font-size:11px; color:#7c3aed; margin-bottom:15px;"><?php echo esc_html( $sender_profile->headline ?: 'Minister of the Gospel' ); ?></div>
                        
                        <div style="display:flex; gap:10px; width:100%;">
                            <form method="post" action="" style="flex:1;">
                                <?php wp_nonce_field( 'mtts_alumni_social' ); ?>
                                <input type="hidden" name="mtts_alumni_action" value="accept_friend_request">
                                <input type="hidden" name="request_id" value="<?php echo $req->id; ?>">
                                <button type="submit" class="mtts-btn mtts-btn-primary" style="width:100%; padding:8px; font-size:12px; border-radius:10px;">Accept</button>
                            </form>
                            <form method="post" action="" style="flex:1;">
                                <?php wp_nonce_field( 'mtts_alumni_social' ); ?>
                                <input type="hidden" name="mtts_alumni_action" value="reject_friend_request">
                                <input type="hidden" name="request_id" value="<?php echo $req->id; ?>">
                                <button type="submit" class="mtts-btn" style="width:100%; padding:8px; font-size:12px; border-radius:10px; background:#f1f5f9; color:#64748b;">Reject</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="koinonia-glass" style="padding:30px; border-radius:15px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
            <h3 style="margin:0; font-size:20px; color:#1e293b;">Active Fellowship</h3>
            <span style="font-size:12px; font-weight:700; color:#64748b; background:#f1f5f9; padding:4px 12px; border-radius:12px;"><?php echo count($friends_data); ?> FRIENDS</span>
        </div>

        <?php if ( empty( $friends_data ) ) : ?>
            <div style="text-align:center; padding:60px 20px; color:#94a3b8;">
                <span class="dashicons dashicons-groups" style="font-size:4rem; width:auto; height:auto; opacity:0.2;"></span>
                <p style="margin-top:20px; font-size:16px;">The harvest is plenty, but your connections are few.</p>
                <a href="?view=directory" class="spiritual-gradient-text" style="text-decoration:none; font-weight:700;">Explore the Directory →</a>
            </div>
        <?php else : ?>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
                <?php foreach ( $friends_data as $friend_rel ) : 
                    $friend_id = ($friend_rel->sender_id == $user->ID) ? $friend_rel->receiver_id : $friend_rel->sender_id;
                    $friend = get_userdata( $friend_id );
                    $friend_profile = \MttsLms\Models\AlumniProfile::get_by_user( $friend_id );
                ?>
                    <div class="mtts-social-card" style="padding:20px; background:#fff; display:flex; align-items:center;">
                        <img src="<?php echo get_avatar_url( $friend_id ); ?>" style="width:55px; height:55px; border-radius:50%; margin-right:15px; border:2px solid #f5f3ff;" alt="">
                        <div style="flex:1;">
                            <div style="font-weight:700; color:#1e293b; font-size:14px;"><?php echo esc_html( $friend->display_name ); ?></div>
                            <div style="font-size:11px; color:#64748b; margin-bottom:8px;"><?php echo esc_html( substr($friend_profile->headline ?: 'Minister', 0, 30) ); ?>...</div>
                            <a href="?view=messenger&chat_with=<?php echo $friend_id; ?>" class="mtts-btn mtts-btn-sm" style="display:inline-block; font-size:10px; padding:4px 12px; border-radius:15px; text-decoration:none;">Propagate Message</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
