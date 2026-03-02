<div class="mtts-dashboard-section" style="max-width:1200px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;">
        <div>
            <h2 class="spiritual-gradient-text" style="margin:0; font-size:28px;">Theological Network</h2>
            <p style="color:#64748b; margin:5px 0 0;">Connecting ministers and scholars across the globe.</p>
        </div>
        <div style="position:relative;">
            <input type="text" placeholder="Search ministers..." style="padding:12px 40px 12px 20px; border-radius:30px; border:1px solid #e2e8f0; background:#fff; width:350px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
            <span class="dashicons dashicons-search" style="position:absolute; right:15px; top:14px; color:#94a3b8;"></span>
        </div>
    </div>

    <?php if ( empty( $alumni ) ) : ?>
        <div class="koinonia-glass mtts-social-card" style="padding:80px; text-align:center;">
            <span class="dashicons dashicons-groups" style="font-size:4rem; width:auto; height:auto; color:#cbd5e1;"></span>
            <p style="font-size:18px; color:#64748b; margin-top:20px;">The fellowship is growing. Be the first to reach out!</p>
        </div>
    <?php else : ?>
        <div class="alumni-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:25px;">
            <?php foreach ( $alumni as $alum ) : 
                $profile = \MttsLms\Models\AlumniProfile::get_by_user( $alum->ID );
                $matric = get_user_meta($alum->ID, 'mtts_matric_number', true);
            ?>
                <div class="koinonia-glass mtts-social-card" style="padding:0; overflow:hidden; text-align:center; position:relative;">
                    <div style="height:70px; background:<?php echo ($profile->banner_url ?? '') ? 'url('.$profile->banner_url.')' : 'linear-gradient(135deg, #f5f3ff, #fffbeb)'; ?>; background-size:cover; border-bottom:1px solid #f1f5f9;"></div>
                    <div style="padding:0 20px 25px 20px;">
                        <img src="<?php echo $profile->profile_picture_url ?? get_avatar_url( $alum->ID ); ?>" style="width:80px; height:80px; border-radius:50%; border:4px solid #fff; margin-top:-40px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); object-fit:cover;" alt="">
                        <h4 style="margin:15px 0 5px; font-size:16px;"><?php echo esc_html( $alum->display_name ); ?></h4>
                        <p style="color:#7c3aed; font-weight:700; font-size:12px; margin:0;"><?php echo esc_html( $profile->headline ?? 'Minister of the Gospel' ); ?></p>
                        <p style="color:#64748b; font-size:11px; margin:5px 0 20px;"><?php echo esc_html( $profile->location ?? 'General Mission Field' ); ?></p>
                        
                        <div style="display:flex; gap:10px; justify-content:center;">
                            <a href="?view=profile&uid=<?php echo $alum->ID; ?>" class="mtts-btn mtts-btn-primary" style="border-radius:20px; padding:6px 15px; font-size:12px; flex:1; text-decoration:none;">View Profile</a>
                            <?php 
                            $request = \MttsLms\Models\FriendRequest::get_request( $user->ID, $alum->ID );
                            if ( $request ) :
                                if ( $request->status === 'accepted' ) : ?>
                                    <a href="?view=messenger&chat_with=<?php echo $alum->ID; ?>" class="mtts-btn" style="border-radius:20px; padding:6px 15px; font-size:12px; background:#f1f5f9; color:#7c3aed; text-decoration:none;">Chat</a>
                                <?php else : ?>
                                    <button disabled class="mtts-btn" style="border-radius:20px; padding:6px 15px; font-size:12px; background:#f1f5f9; color:#94a3b8; cursor:default;">Pending</button>
                                <?php endif;
                            else : ?>
                                <button type="button" class="mtts-btn" style="border-radius:20px; padding:6px 15px; font-size:12px; background:#f1f5f9; color:#7c3aed;" onclick="socialAction('send_friend_request', <?php echo $alum->ID; ?>, this)">Connect</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
