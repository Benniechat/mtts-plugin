<?php
/**
 * Shared Page Header for LMS Redesign
 */
?>
<div class="lms-card lms-card-header-wrapper" style="margin-bottom: 15px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div class="lms-page-title">
            <h1 style="color: var(--lms-purple); font-weight: 700; margin-bottom: 4px;"><?php echo esc_html( $page_title ); ?></h1>
            <?php if ( ! empty( $page_subtitle ) ) : ?>
                <p style="color: var(--lms-text-sub); margin: 0; font-size: 14px; font-style: italic;"><?php echo esc_html( $page_subtitle ); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if ( isset( $header_actions ) ) : ?>
        <div class="lms-header-actions">
            <?php echo $header_actions; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
