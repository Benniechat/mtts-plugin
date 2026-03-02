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
    
    .mtts-admin-container {
        display: flex;
        min-height: 100vh;
    }
    .mtts-sidebar {
        width: 280px;
        background: #1e1b2e; /* Dark slate from Stitch */
        color: #ffffff;
        padding: 32px 0;
        display: flex;
        flex-direction: column;
    }
    .mtts-sidebar-logo {
        padding: 0 32px;
        margin-bottom: 48px;
        font-weight: 700;
        font-size: 20px;
        color: #ea580c;
    }
    .mtts-nav-item {
        padding: 12px 32px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #a5b4fc;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 15px;
    }
    .mtts-nav-item:hover, .mtts-nav-item.active {
        background: rgba(107, 33, 168, 0.2);
        color: #ffffff;
        border-right: 4px solid #ea580c;
    }
    .mtts-nav-item .dashicons {
        font-size: 20px;
        width: 20px;
        height: 20px;
    }
    .mtts-main-content {
        flex: 1;
        background: #f8fafc;
    }
    .mtts-content-inner {
        padding: 40px;
        max-width: 1200px;
        margin: 0 auto;
    }
    .mtts-stat-card {
        background: #ffffff;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .mtts-stat-value {
        font-size: 32px;
        font-weight: 700;
        margin: 10px 0;
        color: #1e1b2e;
    }
    .mtts-stat-trend {
        font-size: 13px;
        font-weight: 600;
    }
    .mtts-stat-trend.up { color: #10b981; }
    .mtts-stat-trend.down { color: #f59e0b; }
</style>

<div class="mtts-admin-container mtts-lms-wrapper">
    <aside class="mtts-sidebar">
        <div class="mtts-sidebar-logo" style="padding: 0 32px; margin-bottom: 32px;">
            <img src="<?php echo MTTS_LMS_URL . 'assets/images/logo-mtts.jpg'; ?>" alt="Logo" style="height: 40px; border-radius: 4px;">
        </div>
        <nav>
            <a href="#" class="mtts-nav-item active">
                <span class="dashicons dashicons-dashboard"></span> Dashboard
            </a>
            <a href="#" class="mtts-nav-item">
                <span class="dashicons dashicons-groups"></span> Student Records
            </a>
            <a href="#" class="mtts-nav-item">
                <span class="dashicons dashicons-book-alt"></span> Course Registry
            </a>
            <a href="#" class="mtts-nav-item">
                <span class="dashicons dashicons-shield"></span> Document Verification
            </a>
            <a href="#" class="mtts-nav-item">
                <span class="dashicons dashicons-analytics"></span> Reports & Data
            </a>
            <a href="#" class="mtts-nav-item">
                <span class="dashicons dashicons-admin-settings"></span> System Settings
            </a>
        </nav>
    </aside>

    <main class="mtts-main-content">
        <header style="background: #ffffff; padding: 24px 40px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 20px; font-weight: 700; color: #1e1b2e;">Registrar Management Portal</div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 15px;">Dr. Aris Thorne</div>
                    <div style="font-size: 12px; color: #64748b;">Lead Registrar</div>
                </div>
                <div style="width: 44px; height: 44px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #6b21a8;">
                    <span class="dashicons dashicons-admin-users" style="color: #6b21a8;"></span>
                </div>
            </div>
        </header>

        <div class="mtts-content-inner">
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 28px; font-weight: 700; color: #1e1b2e; margin-bottom: 8px;">Academic Overview</h2>
                <p style="color: #64748b; font-size: 15px;">Status report for Fall Semester 2024 admissions and registrar actions.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px;">
                <div class="mtts-stat-card">
                    <div style="color: #64748b; font-size: 14px; font-weight: 600;">New Registrations</div>
                    <div class="mtts-stat-value">1,248</div>
                    <div class="mtts-stat-trend up">↑ 65% of target reached</div>
                </div>
                <div class="mtts-stat-card">
                    <div style="color: #64748b; font-size: 14px; font-weight: 600;">Verified Documents</div>
                    <div class="mtts-stat-value">856</div>
                    <div class="mtts-stat-trend up">↑ 88% Completion rate</div>
                </div>
                <div class="mtts-stat-card">
                    <div style="color: #64748b; font-size: 14px; font-weight: 600;">Pending Approvals</div>
                    <div class="mtts-stat-value">42</div>
                    <div class="mtts-stat-trend down">↓ Down 5% from yesterday</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <div style="background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h3 style="font-size: 18px; font-weight: 700; color: #1e1b2e;">Recent Student Applications</h3>
                        <a href="#" style="color: #6b21a8; font-size: 14px; font-weight: 600; text-decoration: none;">View All</a>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                            <div>
                                <div style="font-weight: 600;">Elijah Samuel</div>
                                <div style="font-size: 12px; color: #64748b;">ID: #MTS-9021</div>
                            </div>
                            <span style="background: #f5f3ff; color: #6b21a8; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Under Review</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                            <div>
                                <div style="font-weight: 600;">Hannah Adebayo</div>
                                <div style="font-size: 12px; color: #64748b;">ID: #MTS-8842</div>
                            </div>
                            <span style="background: #fdf2f8; color: #db2777; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Urgent</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                            <div>
                                <div style="font-weight: 600;">James Crawford</div>
                                <div style="font-size: 12px; color: #64748b;">ID: #MTS-8551</div>
                            </div>
                            <span style="background: #f0fdf4; color: #16a34a; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Verified</span>
                        </div>
                    </div>
                </div>

                <div>
                    <div style="background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
                        <h3 style="font-size: 18px; font-weight: 700; color: #1e1b2e; margin-bottom: 16px;">Upcoming Deadlines</h3>
                        <div style="margin-bottom: 16px; border-left: 3px solid #6b21a8; padding-left: 12px;">
                            <div style="font-weight: 700; color: #6b21a8;">Registration Freeze</div>
                            <div style="font-size: 12px; color: #64748b;">All Fall '24 applications must be processed by 5 PM.</div>
                        </div>
                        <div style="border-left: 3px solid #ea580c; padding-left: 12px;">
                            <div style="font-weight: 700; color: #ea580c;">Term Opening Ceremony</div>
                            <div style="font-size: 12px; color: #64748b;">Faculty and Staff mandatory attendance.</div>
                        </div>
                    </div>

                    <div style="background: #6b21a8; padding: 24px; border-radius: 12px; color: #ffffff;">
                        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">System Integrity</h3>
                        <p style="font-size: 13px; opacity: 0.9; line-height: 1.5;">Next database maintenance scheduled for Saturday, 2 AM EST.</p>
                        <button style="margin-top: 16px; width: 100%; padding: 10px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; color: white; cursor: pointer; font-weight: 600;">Run Diagnostic</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
