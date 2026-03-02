<?php
/**
 * Lecturer Dashboard View (Stitch Rebuild)
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');
    
    .mtts-lecturer-wrapper {
        font-family: 'Lexend', sans-serif;
        background: #f8fafc;
        padding: 40px;
        color: #1e293b;
    }
    .mtts-hero-banner {
        background: linear-gradient(135deg, #6b21a8 0%, #ea580c 100%);
        border-radius: 12px;
        padding: 40px;
        color: #ffffff;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
    }
    .mtts-hero-banner h2 {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 12px 0;
        color: #ffffff;
    }
    .mtts-hero-banner p {
        font-size: 18px;
        opacity: 0.9;
        margin: 0;
    }
    .mtts-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    .mtts-stat-card {
        background: #ffffff;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .mtts-stat-label {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 12px;
        font-weight: 500;
    }
    .mtts-stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #6b21a8;
    }
    .mtts-stat-trend {
        font-size: 12px;
        margin-top: 8px;
        color: #10b981;
    }
    .mtts-main-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    .mtts-card {
        background: #ffffff;
        padding: 32px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .mtts-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .mtts-card-title {
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .mtts-card-title .dashicons {
        color: #6b21a8;
    }
    .mtts-list-item {
        padding: 16px;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 12px;
        border: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .mtts-badge {
        background: #f5f3ff;
        color: #6b21a8;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="mtts-lecturer-wrapper">
    <div class="mtts-hero-banner">
        <h2>Shalom, Professor!</h2>
        <p>Your next seminar on "History of African Theology" starts in 15 minutes.</p>
        <div style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); opacity: 0.1;">
            <span class="dashicons dashicons-welcome-learn-more" style="font-size: 120px; width: 120px; height: 120px;"></span>
        </div>
    </div>

    <div class="mtts-stats-grid">
        <div class="mtts-stat-card">
            <div class="mtts-stat-label">Active Courses</div>
            <div class="mtts-stat-value">4</div>
        </div>
        <div class="mtts-stat-card">
            <div class="mtts-stat-label">Pending Grading</div>
            <div class="mtts-stat-value">28</div>
            <div class="mtts-stat-trend">Submissions today</div>
        </div>
        <div class="mtts-stat-card">
            <div class="mtts-stat-label">Student Messages</div>
            <div class="mtts-stat-value">45</div>
        </div>
        <div class="mtts-stat-card">
            <div class="mtts-stat-label">Avg. Engagement</div>
            <div class="mtts-stat-value">High</div>
        </div>
    </div>

    <div class="mtts-main-layout">
        <div class="mtts-left-col">
            <div class="mtts-card">
                <div class="mtts-card-header">
                    <div class="mtts-card-title"><span class="dashicons dashicons-clipboard"></span> Pending Assignments Submission</div>
                </div>
                <div class="mtts-list-item">
                    <div>
                        <strong style="display: block;">Old Testament Survey</strong>
                        <span style="font-size: 13px; color: #64748b;">42 Enrolled Students</span>
                    </div>
                    <span class="mtts-badge">Grade Now</span>
                </div>
                <div class="mtts-list-item">
                    <div>
                        <strong style="display: block;">Philosophy of Religion</strong>
                        <span style="font-size: 13px; color: #64748b;">28 Enrolled Students</span>
                    </div>
                    <span class="mtts-badge">12 New</span>
                </div>
            </div>

            <div class="mtts-card">
                <div class="mtts-card-header">
                    <div class="mtts-card-title"><span class="dashicons dashicons-email"></span> Student Inbox</div>
                </div>
                <div class="mtts-list-item" style="display: block;">
                    <div style="font-size: 14px; color: #64748b; margin-bottom: 5px;">Professor, I'm having trouble accessing the module 4 reading...</div>
                    <div style="font-weight: 600; font-size: 12px; color: #6b21a8;">John Samuel • 2h ago</div>
                </div>
                <div class="mtts-list-item" style="display: block;">
                    <div style="font-size: 14px; color: #64748b; margin-bottom: 5px;">Thank you for the extension on the paper!</div>
                    <div style="font-weight: 600; font-size: 12px; color: #6b21a8;">Sarah Miller • 5h ago</div>
                </div>
            </div>
        </div>

        <div class="mtts-right-col">
            <div class="mtts-card">
                <div class="mtts-card-title" style="margin-bottom: 20px;"><span class="dashicons dashicons-megaphone"></span> Faculty Notices</div>
                
                <div style="margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                    <strong style="font-size: 15px; color: #6b21a8; display: block; margin-bottom: 4px;">Grade submission deadline</strong>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">Please ensure all midterm results are uploaded by Friday, 5 PM.</p>
                </div>

                <div>
                    <strong style="font-size: 15px; color: #6b21a8; display: block; margin-bottom: 4px;">New Library Resources</strong>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">E-access to the Vatican Archives is now available for all faculty.</p>
                </div>
            </div>
            
            <div style="background: #ffffff; border-radius: 12px; border: 1px dashed #cbd5e1; padding: 24px; text-align: center;">
                <span class="dashicons dashicons-plus-alt" style="font-size: 32px; width: 32px; height: 32px; color: #94a3b8; margin-bottom: 12px;"></span>
                <div style="font-weight: 600; font-size: 14px; color: #64748b;">Schedule Office Hours</div>
            </div>
        </div>
    </div>
</div>
