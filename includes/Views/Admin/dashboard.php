<?php
/**
 * Admin Dashboard View (Stitch Rebuild)
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');
    
    .mtts-admin-wrapper {
        font-family: 'Lexend', sans-serif;
        background: #f8fafc;
        min-height: 100vh;
        color: #1e293b;
    }
    .mtts-header {
        background: #ffffff;
        padding: 24px 40px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .mtts-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #144bb8;
        margin: 0;
    }
    .mtts-content {
        padding: 40px;
        max-width: 1400px;
        margin: 0 auto;
    }
    .mtts-overview-header {
        margin-bottom: 40px;
    }
    .mtts-overview-header h2 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .mtts-overview-header p {
        color: #64748b;
        font-size: 18px;
    }
    .mtts-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }
    .mtts-stat-card {
        background: #ffffff;
        padding: 32px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }
    .mtts-stat-label {
        color: #64748b;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 12px;
    }
    .mtts-stat-value {
        font-size: 36px;
        font-weight: 700;
        color: #144bb8;
    }
    .mtts-main-sections {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    .mtts-section-card {
        background: #ffffff;
        padding: 32px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .mtts-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .mtts-section-title {
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .mtts-section-title .dashicons {
        color: #144bb8;
    }
    .mtts-btn-link {
        color: #144bb8;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }
    .mtts-data-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .mtts-data-item {
        padding: 16px 0;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .mtts-data-item:last-child {
        border-bottom: none;
    }
    .mtts-item-info strong {
        display: block;
        font-size: 16px;
    }
    .mtts-item-info span {
        color: #64748b;
        font-size: 14px;
    }
</style>

<div class="mtts-admin-wrapper">
    <div class="mtts-header">
        <h1>Mountain-Top Theological Seminary</h1>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="text-align: right;">
                <div style="font-weight: 600;">Admin User</div>
                <div style="font-size: 12px; color: #64748b;">Super Administrator</div>
            </div>
            <div style="width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span class="dashicons dashicons-admin-users"></span>
            </div>
        </div>
    </div>

    <div class="mtts-content">
        <div class="mtts-overview-header">
            <h2>Dashboard Overview</h2>
            <p>Institutional performance and management control.</p>
        </div>

        <div class="mtts-stats-grid">
            <div class="mtts-stat-card">
                <div class="mtts-stat-label">Total Students</div>
                <div class="mtts-stat-value">1,240</div>
            </div>
            <div class="mtts-stat-card">
                <div class="mtts-stat-label">Active Lecturers</div>
                <div class="mtts-stat-value">45</div>
            </div>
            <div class="mtts-stat-card">
                <div class="mtts-stat-label">Total Revenue</div>
                <div class="mtts-stat-value">$158,400</div>
            </div>
        </div>

        <div class="mtts-main-sections">
            <div class="mtts-left-col">
                <div class="mtts-section-card">
                    <div class="mtts-section-header">
                        <div class="mtts-section-title">
                            <span class="dashicons dashicons-id"></span>
                            Pending Applications
                        </div>
                        <a href="<?php echo admin_url('admin.php?page=mtts-applications'); ?>" class="mtts-btn-link">View All</a>
                    </div>
                    <ul class="mtts-data-list">
                        <li class="mtts-data-item">
                            <div class="mtts-item-info">
                                <strong>John Doe</strong>
                                <span>j.doe@email.com</span>
                            </div>
                            <span style="background: #f1f5f9; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">Under Review</span>
                        </li>
                        <li class="mtts-data-item">
                            <div class="mtts-item-info">
                                <strong>Sarah Miller</strong>
                                <span>s.miller@email.com</span>
                            </div>
                            <span style="background: #f1f5f9; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">Pending</span>
                        </li>
                        <li class="mtts-data-item">
                            <div class="mtts-item-info">
                                <strong>Abraham Kwesi</strong>
                                <span>akwesi@email.com</span>
                            </div>
                            <span style="background: #f1f5f9; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">Awaiting Doc</span>
                        </li>
                    </ul>
                </div>

                <div class="mtts-section-card">
                    <div class="mtts-section-header">
                        <div class="mtts-section-title">
                            <span class="dashicons dashicons-analytics"></span>
                            Quick Reports
                        </div>
                    </div>
                    <p style="color: #64748b; margin-bottom: 20px;">Instantly generate PDF or Excel reports for various seminary departments.</p>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <button class="button" style="padding: 10px; height: auto;">Students Record</button>
                        <button class="button" style="padding: 10px; height: auto;">Financial Report</button>
                        <button class="button" style="padding: 10px; height: auto;">LMS Engagement</button>
                        <button class="button" style="padding: 10px; height: auto;">Faculty Load</button>
                    </div>
                </div>
            </div>

            <div class="mtts-right-col">
                <div class="mtts-section-card">
                    <div class="mtts-section-header">
                        <div class="mtts-section-title">
                            <span class="dashicons dashicons-calendar-alt"></span>
                            Academic Calendar
                        </div>
                    </div>
                    <div style="border-left: 2px solid #144bb8; padding-left: 15px; margin-bottom: 20px;">
                        <div style="font-weight: 600; font-size: 15px;">Mid-Semester Examinations</div>
                        <div style="color: #64748b; font-size: 13px;">Nov 12 - Nov 18, 2023</div>
                    </div>
                    <div style="border-left: 2px solid #e2e8f0; padding-left: 15px;">
                        <div style="font-weight: 600; font-size: 15px;">Seminar: Comparative Theology</div>
                        <div style="color: #64748b; font-size: 13px;">Dec 05, 2023</div>
                    </div>
                </div>

                <div class="mtts-section-card">
                    <div class="mtts-section-title" style="margin-bottom: 15px;">
                        <span class="dashicons dashicons-megaphone"></span>
                        Faculty Notices
                    </div>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 15px; color: #1e40af; font-size: 14px; line-height: 1.5;">
                        <strong>Staff Meeting tomorrow</strong><br>
                        Reminder: General staff meeting via Zoom at 10:00 AM.
                    </div>
                </div>

                <div class="mtts-section-card">
                    <div class="mtts-section-title" style="margin-bottom: 15px;">
                        <span class="dashicons dashicons-awards"></span>
                        Smart ID Generation
                    </div>
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 15px;">Auto-generate secure digital and physical ID cards for approved students.</p>
                    <a href="#" class="button button-primary" style="background: #144bb8; border: none; width: 100%; text-align: center;">Generate Bulk IDs</a>
                </div>
            </div>
        </div>
    </div>
</div>
