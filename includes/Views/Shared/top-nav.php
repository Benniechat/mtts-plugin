<?php
/**
 * Shared Top Navigation for MFM LMS Enterprise UI
 */
$user = wp_get_current_user();
?>
<div class="mtts-top-nav">
    <div class="mtts-top-nav-left">
        <div class="mtts-logo-container">
            <!-- MFM Logo -->
            <img src="<?php echo MTTS_LMS_URL . 'assets/images/mfm-logo.png'; ?>" alt="MFM Logo" onerror="this.style.display='none'">
        </div>
        <div class="mtts-platform-name">
            MFM Theological Seminary LMS
        </div>
    </div>

    <div class="mtts-top-nav-center">
        <div class="mtts-global-search">
            <span class="dashicons dashicons-search" style="position: absolute; left: 12px; top: 10px; color: rgba(255,255,255,0.4);"></span>
            <input type="text" placeholder="Search students, courses, networks...">
        </div>
    </div>

    <div class="mtts-top-nav-right">
        <div class="mtts-icon-action">
            <span class="dashicons dashicons-email"></span>
            <?php 
            $unread_msg = \MttsLms\Models\Message::count_unread( $user->ID );
            if ( $unread_msg > 0 ) : ?>
                <span class="mtts-badge-count"><?php echo $unread_msg; ?></span>
            <?php endif; ?>
        </div>
        
        <div class="mtts-icon-action">
            <span class="dashicons dashicons-bell"></span>
            <span class="mtts-badge-count">3</span> <!-- Placeholder for actual notifications -->
        </div>

        <div class="mtts-user-dropdown" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <img src="<?php echo get_avatar_url( $user->ID ); ?>" class="mtts-avatar" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid var(--mfm-gold);">
            <span class="dashicons dashicons-arrow-down-alt2" style="font-size: 14px; width: 14px; height: 14px; opacity: 0.7;"></span>
        </div>
    </div>
</div>
