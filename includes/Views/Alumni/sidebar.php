<!-- Left Sidebar Column: Facebook Style -->
<div class="lms-sidebar-card fb-sidebar-nav" style="background:transparent; shadow:none; border:none; padding: 0;">
    <?php 
    $user_id = get_current_user_id();
    $profile = \MttsLms\Models\AlumniProfile::get_by_user( $user_id );
    $avatar_url = $profile->profile_picture_url ?: get_avatar_url( $user_id );
    ?>
    
    <ul class="lms-side-nav-list fb-nav-list" style="padding: 0;">
        <li style="margin-bottom: 8px; padding-left: 8px;">
            <h3 style="font-size: 18px; font-weight: 800; color: var(--lms-purple); margin: 0; letter-spacing: -0.5px;">MTTS Connect+</h3>
        </li>
        <li style="margin-bottom: 2px;">
            <a href="?view=profile" style="padding: 8px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
                <img src="<?php echo $avatar_url; ?>" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                <span><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
            </a>
        </li>
        <li>
            <a href="?view=friends" style="padding: 8px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
                <span class="dashicons dashicons-groups" style="color: #1877F2; font-size: 24px; width: 36px; height: 36px; background: #e7f3ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;"></span>
                <span>Fellowship Circle</span>
            </a>
        </li>
        <li>
            <a href="?view=groups" style="padding: 8px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
                <span class="dashicons dashicons-category" style="color: #2ABBA7; font-size: 24px; width: 36px; height: 36px; background: #eaf8f6; border-radius: 50%; display: flex; align-items: center; justify-content: center;"></span>
                <span>Ministry Circles</span>
            </a>
        </li>
        <li>
            <a href="?view=messenger" style="padding: 8px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
                <span class="dashicons dashicons-email-alt" style="color: #B426B6; font-size: 24px; width: 36px; height: 36px; background: #f7ebf7; border-radius: 50%; display: flex; align-items: center; justify-content: center;"></span>
                <span>Messenger</span>
            </a>
        </li>
        <li>
            <a href="?view=events" style="padding: 8px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
                <span class="dashicons dashicons-calendar-alt" style="color: #F35369; font-size: 24px; width: 36px; height: 36px; background: #feeff1; border-radius: 50%; display: flex; align-items: center; justify-content: center;"></span>
                <span>Events</span>
            </a>
        </li>
        <li>
            <a href="?view=feed" style="padding: 8px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
                <span class="dashicons dashicons-share" style="color: #1877F2; font-size: 24px; width: 36px; height: 36px; background: #e7f3ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;"></span>
                <span>Covenant Feed</span>
            </a>
        </li>
    </ul>
    
    <div style="border-top: 1px solid #CED0D4; margin: 8px 16px;"></div>
    
    <ul class="lms-side-nav-list fb-nav-list" style="padding: 0;">
         <li>
            <a href="?view=directory" style="padding: 8px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
                <span class="dashicons dashicons-id-alt" style="color: #F7B928; font-size: 24px; width: 36px; height: 36px; background: #fff8e2; border-radius: 50%; display: flex; align-items: center; justify-content: center;"></span>
                <span>Directory</span>
            </a>
        </li>
        <li>
            <a href="?view=jobs" style="padding: 8px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
                <span class="dashicons dashicons-businessman" style="color: #E29E2F; font-size: 24px; width: 36px; height: 36px; background: #fff4e5; border-radius: 50%; display: flex; align-items: center; justify-content: center;"></span>
                <span>Ministry Jobs</span>
            </a>
        </li>
    </ul>
</div>

<div class="lms-sidebar-card" style="margin-top: 12px;">
    <ul class="lms-side-nav-list">
        <li>
            <a href="<?php echo wp_logout_url( home_url() ); ?>" style="color:var(--mfm-danger);">
                <span class="dashicons dashicons-exit"></span> Logout
            </a>
        </li>
    </ul>
</div>

