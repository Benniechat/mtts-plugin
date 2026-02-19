<style>
    .alumni-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .profile-card {
        padding: 20px;
        text-align: center;
    }
    .profile-card img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid var(--mtts-gold);
        margin-bottom: 15px;
        object-fit: cover;
    }
    .profile-card h4 {
        margin: 10px 0 5px;
        color: var(--mtts-purple);
        font-weight: 700;
    }
    .profile-card p {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 15px;
    }
</style>

<div class="mtts-dashboard-section" style="max-width:1000px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <h2 style="color:var(--mtts-purple); margin:0;">Covenant Directory</h2>
        <div style="position:relative;">
            <input type="text" placeholder="Search fellow alumni..." style="padding:10px 40px 10px 20px; border-radius:25px; border:1px solid rgba(0,0,0,0.1); background:var(--mtts-glass); width:300px;">
            <span class="dashicons dashicons-search" style="position:absolute; right:15px; top:12px; color:#666;"></span>
        </div>
    </div>

    <?php if ( empty( $alumni ) ) : ?>
        <div class="glass-card" style="padding:50px; text-align:center; color:#888;">
            <span class="dashicons dashicons-groups" style="font-size:3rem; width:auto; height:auto; opacity:0.3;"></span>
            <p>No alumni found. Invite your classmates to join the covenant!</p>
        </div>
    <?php else : ?>
        <div class="alumni-grid">
            <?php foreach ( $alumni as $alum ) : 
                $profile = \MttsLms\Models\AlumniProfile::get_by_user( $alum->ID );
                $matric = get_user_meta($alum->ID, 'mtts_matric', true);
            ?>
                <div class="glass-card profile-card">
                    <img src="<?php echo get_avatar_url( $alum->ID ); ?>" alt="<?php echo esc_html( $alum->display_name ); ?>">
                    <h4><?php echo esc_html( $alum->display_name ); ?></h4>
                    <p style="font-style:italic;"><?php echo esc_html( $profile->headline ?: 'Minister of the Gospel' ); ?></p>
                    
                    <div style="font-size:0.75rem; color:#666; margin-bottom:20px; min-height:40px;">
                        <span class="dashicons dashicons-location" style="font-size:0.9rem; margin-right:3px;"></span>
                        <?php echo esc_html( $profile->current_ministry ?: 'General Mission Field' ); ?>
                    </div>

                    <div style="display:flex; gap:10px; justify-content:center;">
                        <form method="post" action="?view=friends">
                            <?php wp_nonce_field('mtts_send_friend_request'); ?>
                            <input type="hidden" name="mtts_action" value="send_request">
                            <input type="hidden" name="receiver_id" value="<?php echo $alum->ID; ?>">
                            <button type="submit" class="mtts-btn mtts-btn-sm" style="background:var(--mtts-purple); color:#fff; border-radius:20px; padding:6px 15px; font-size:0.75rem;">Connect</button>
                        </form>
                        <?php if ( $matric ) : ?>
                            <a href="?view=portfolio&matric=<?php echo esc_attr($matric); ?>" class="mtts-btn mtts-btn-sm" style="background:rgba(255,255,255,0.5); border:1px solid rgba(0,0,0,0.1); border-radius:20px; padding:6px 15px; font-size:0.75rem; color:var(--mtts-purple);">Portfolio</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
