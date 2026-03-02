<?php
/**
 * Internal LMS Sub-Navigation (Scoped)
 * Part of the WordPress-Compatible Redesign
 */
$user = wp_get_current_user();
?>
<div class="lms-sub-nav">
    <div class="lms-sub-nav-left">
        <h3 style="margin:0; font-size: 14px; color: var(--lms-purple);">
            <span class="dashicons dashicons-welcome-learn-more" style="font-size: 18px; margin-right: 8px;"></span>
            LMS Ministerial Portal
        </h3>
    </div>

    <div class="lms-sub-nav-right" style="display: flex; align-items: center; gap: 20px;">
        <!-- Messaging Icon -->
        <a href="?view=messenger" class="lms-icon-btn" title="Propagate Messenger">
            <span class="dashicons dashicons-email"></span>
            <?php 
            $unread_msg = \MttsLms\Models\Message::count_unread( $user->ID );
            if ( $unread_msg > 0 ) : ?>
                <span class="mtts-badge-count"><?php echo $unread_msg; ?></span>
            <?php endif; ?>
        </a>
        
        <!-- Notifications -->
        <a href="#" class="lms-icon-btn" title="Notifications">
            <span class="dashicons dashicons-bell"></span>
            <span class="mtts-badge-count">3</span>
        </a>

        <!-- User Identity -->
        <div class="lms-user-pill" style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600;">
            <img src="<?php echo get_avatar_url( $user->ID ); ?>" style="width: 24px; height: 24px; border-radius: 50%;">
            <span><?php echo esc_html( $user->display_name ); ?></span>
        </div>
    </div>
</div>
