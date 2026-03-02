<?php
/**
 * Student Dashboard View (Stitch Rebuild)
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');
    
    .mtts-student-wrapper {
        font-family: 'Lexend', sans-serif;
        background: #f8fafc;
        padding: 40px;
        color: #1e293b;
    }
    .mtts-welcome-banner {
        background: #ffffff;
        padding: 32px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .mtts-welcome-banner h2 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }
    .mtts-welcome-banner p {
        color: #64748b;
        margin: 0;
    }
    .mtts-stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    .mtts-tracker-card {
        background: #ffffff;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
    }
    .mtts-tracker-label {
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .mtts-tracker-value {
        font-size: 32px;
        font-weight: 700;
        color: #6b21a8;
    }
    .mtts-tracker-sub {
        font-size: 12px;
        color: #10b981;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .mtts-grid-2-1 {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    .mtts-card {
        background: #ffffff;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .mtts-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .mtts-card-title {
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .mtts-card-title .dashicons {
        color: #6b21a8;
    }
    .mtts-lecture-item {
        padding: 16px;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #f1f5f9;
    }
    .mtts-lecture-info strong {
        display: block;
        font-size: 15px;
    }
    .mtts-lecture-info span {
        font-size: 13px;
        color: #64748b;
    }
    .mtts-zoom-btn {
        background: #ea580c;
        color: #fff;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
    }
    .mtts-assignment-item {
        padding: 16px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .mtts-assignment-item:last-child {
        border-bottom: none;
    }
    .mtts-assignment-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .mtts-deadline {
        font-size: 12px;
        color: #ef4444;
        font-weight: 600;
    }
    .mtts-id-preview {
        background: linear-gradient(135deg, #6b21a8 0%, #ea580c 100%);
        padding: 24px;
        border-radius: 12px;
        color: #ffffff;
        text-align: center;
    }
</style>

<div class="mtts-student-wrapper">
    <div class="mtts-welcome-banner">
        <div>
            <h2>Welcome back, <?php echo esc_html( $student->applicant_name ); ?></h2>
            <p>Matric Number: <?php echo esc_html( $student->matric_number ?: 'Pending' ); ?></p>
        </div>
        <div style="text-align: right;">
            <div style="font-weight: 700; color: #6b21a8;"><?php echo esc_html($student->current_level); ?></div>
            <div style="font-size: 14px; color: #64748b;">Undergraduate</div>
        </div>
    </div>

    <div class="mtts-stats-row">
        <?php 
            $session = \MttsLms\Models\Session::get_active_session();
            $current_gpa = $session ? \MttsLms\Core\Grades::calculate_gpa( $student->id, $session->id ) : 0.00;
        ?>
        <div class="mtts-tracker-card">
            <div class="mtts-tracker-label"><span class="dashicons dashicons-analytics"></span> Current GPA</div>
            <div class="mtts-tracker-value"><?php echo number_format($current_gpa, 2); ?></div>
            <div class="mtts-tracker-sub"><span class="dashicons dashicons-trending-up" style="font-size: 14px; width: 14px; height: 14px;"></span> +0.15 this semester</div>
        </div>
        <div class="mtts-tracker-card">
            <div class="mtts-tracker-label"><span class="dashicons dashicons-welcome-learn-more"></span> Credit Hours</div>
            <div class="mtts-tracker-value">18/22</div>
            <div class="mtts-tracker-sub" style="color: #64748b;">Enrolled this session</div>
        </div>
        <div class="mtts-tracker-card">
            <div class="mtts-tracker-label"><span class="dashicons dashicons-yes-alt"></span> Attendance</div>
            <div class="mtts-tracker-value">92%</div>
            <div class="mtts-tracker-sub" style="color: #10b981;"><span class="dashicons dashicons-shield" style="font-size: 14px; width: 14px; height: 14px;"></span> On Track</div>
        </div>
    </div>

    <div class="mtts-grid-2-1">
        <div class="mtts-left-col">
            <div class="mtts-card">
                <div class="mtts-card-header">
                    <div class="mtts-card-title"><span class="dashicons dashicons-video-alt3"></span> Upcoming Zoom Lectures</div>
                </div>
                <div class="mtts-lecture-item">
                    <div class="mtts-lecture-info">
                        <strong>Systematic Theology II</strong>
                        <span>Introduction to Pneumatology • Today, 10:00 AM</span>
                    </div>
                    <a href="#" class="mtts-zoom-btn">Join Lecture</a>
                </div>
                <div class="mtts-lecture-item">
                    <div class="mtts-lecture-info">
                        <strong>Biblical Ethics</strong>
                        <span>Ethical Frameworks in Ministry • Tomorrow, 2:00 PM</span>
                    </div>
                    <a href="#" class="mtts-zoom-btn">Link Active Soon</a>
                </div>
            </div>

            <div class="mtts-card">
                <div class="mtts-card-header">
                    <div class="mtts-card-title"><span class="dashicons dashicons-edit"></span> Assignment Submissions</div>
                </div>
                <div class="mtts-assignment-item">
                    <div class="mtts-assignment-meta">
                        <strong>Greek Translation Project</strong>
                        <span class="mtts-deadline">Due: Oct 24, 11:59 PM</span>
                    </div>
                    <div style="font-size: 13px; color: #64748b;">New Testament Studies III</div>
                </div>
                <div class="mtts-assignment-item">
                    <div class="mtts-assignment-meta">
                        <strong>Church History Essay</strong>
                        <span class="mtts-deadline">Due: Oct 30, 05:00 PM</span>
                    </div>
                    <div style="font-size: 13px; color: #64748b;">Reformation Era</div>
                </div>
            </div>
        </div>

        <div class="mtts-right-col">
            <div class="mtts-card">
                <div class="mtts-card-title" style="margin-bottom: 15px;"><span class="dashicons dashicons-megaphone"></span> Announcements</div>
                <div style="margin-bottom: 20px;">
                    <strong style="font-size: 14px; display: block; margin-bottom: 4px;">Mid-Semester Exam Schedule</strong>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">The mid-semester exam timetable is now available on the portal.</p>
                </div>
                <div style="margin-bottom: 20px;">
                    <strong style="font-size: 14px; display: block; margin-bottom: 4px;">Chapel Service Attendance</strong>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">Mandatory morning devotion continues this Wednesday.</p>
                </div>
                <div>
                    <strong style="font-size: 14px; display: block; margin-bottom: 4px;">Library New Arrivals</strong>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">200+ new e-books on Hermeneutics have been added.</p>
                </div>
            </div>

            <div class="mtts-card" style="padding: 12px;">
                <div class="mtts-id-preview">
                    <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.8; margin-bottom: 15px;">Mountain-Top Seminary Digital ID</div>
                    <div style="width: 80px; height: 80px; background: #fff; border-radius: 50%; margin: 0 auto 15px auto; display: flex; align-items: center; justify-content: center; color: #6b21a8;">
                        <span class="dashicons dashicons-admin-users" style="font-size: 40px; width: 40px; height: 40px;"></span>
                    </div>
                    <div style="font-weight: 700; font-size: 16px;"><?php echo esc_html(strtoupper($student->applicant_name)); ?></div>
                    <div style="font-size: 12px; opacity: 0.9; margin-top: 5px;">ID: <?php echo esc_html($student->matric_number ?: 'PENDING'); ?></div>
                    <div style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 15px; font-size: 10px;">VALID THROUGH 2024 SESSION</div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="mailto:support@mttseminary.org" style="font-size: 12px; color: #64748b; text-decoration: none;">Need help? Contact IT Support</a>
            </div>
        </div>
    </div>
</div>
