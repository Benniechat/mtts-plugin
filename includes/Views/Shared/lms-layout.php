<?php
/**
 * Scoped LMS Layout (LinkedIn/Facebook Inspired)
 * Replaces the Enterprise Shell for Theme Compatibility
 */
?>
<div class="mtts-lms-wrapper <?php echo isset($wrapper_class) ? esc_attr($wrapper_class) : ''; ?>">
    
    <!-- Internal Sub-Navigation -->
    <?php include MTTS_LMS_PATH . 'includes/Views/Shared/lms-sub-nav.php'; ?>

    <div class="lms-feed-container">
        
        <!-- Column 1: Left Sidebar -->
        <aside class="lms-sidebar-column">
            <?php 
                // Context-aware sidebar inclusion
                if ( isset($sidebar_path) && file_exists($sidebar_path) ) {
                    include $sidebar_path;
                }
            ?>
        </aside>

        <!-- Column 2: Main Feed -->
        <main class="lms-main-feed">
            <?php 
                // Include Page Header (Title/Actions)
                include MTTS_LMS_PATH . 'includes/Views/Shared/page-header.php';
                
                // Echo Captured Content from Controller
                if ( isset($lms_content) ) {
                    echo $lms_content;
                }
            ?>
        </main>

        <!-- Column 3: Right Utility Panel -->
        <aside class="lms-right-panel">
            <div class="lms-sidebar-card">
                <div class="lms-card-header" style="padding: 12px 16px; border:none; margin:0;">
                    <h3 style="font-size:14px; margin:0;">Upcoming Fellowship</h3>
                </div>
                <div style="padding: 0 16px 16px; font-size: 13px; color: var(--lms-text-sub);">
                    <p>Bible Study - Tomorrow 6PM</p>
                    <a href="#" class="lms-btn lms-btn-outline" style="width:100%;">Set Reminder</a>
                </div>
            </div>
            
            <div class="lms-sidebar-card">
                <div class="lms-card-header" style="padding: 12px 16px; border:none; margin:0;">
                    <h3 style="font-size:14px; margin:0;">LMS Support</h3>
                </div>
                <div style="padding: 0 16px 16px; font-size:13px;">
                    <p>Need help? Contact the IT Propagator.</p>
                </div>
            </div>
        </aside>

    </div>
</div>
