<?php
/**
 * Admin view for Shortcode Bank
 */

use MttsLms\Core\ShortcodeBank;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get shortcodes from the bank (we'll need to expose them or replicate the list)
$shortcodes = array(
    'mtts_user_name'    => 'Displays the current user\'s display name.',
    'mtts_user_email'   => 'Displays the current user\'s email address.',
    'mtts_user_matric'  => 'Displays the current student\'s matriculation number.',
    'mtts_user_program' => 'Displays the current student\'s enrolled program name.',
    'mtts_user_level'   => 'Displays the current student\'s academic level.',
    'mtts_today'        => 'Displays today\'s date in the site\'s format.',
    'mtts_site_name'    => 'Displays the name of the website.',
    'mtts_portal_url'   => 'Displays the URL to the portal login page.',
    'mtts_dashboard_url'=> 'Displays the dynamic dashboard URL based on user role.',
    'mtts_id_card'      => 'Renders the student ID card component.',
    'mtts_exam_results' => 'Renders the student exam results component.',
    'mtts_wallet'       => 'Renders the student wallet/balance component.',
    'mtts_badges'       => 'Renders the student\'s earned badges.',
    'mtts_translator'         => 'Renders the Google Translate widget.',
    'mtts_noticeboard'        => 'Renders the upcoming events noticeboard.',
    'mtts_alumni_dashboard'   => 'Renders the complete Alumni and Community Network dashboard.',
    'mtts_alumni_directory'   => 'Renders a standalone alumni listing and directory.',
);
?>

<div class="wrap" style="background: #0f172a; color: #f8fafc; padding: 30px; border-radius: 12px; margin-top: 20px; font-family: 'Inter', sans-serif; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);">
    <div style="display: flex; align-items: center; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px;">
        <div style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <span class="dashicons dashicons-editor-code" style="color: #fff; font-size: 24px; width: 24px; height: 24px;"></span>
        </div>
        <div>
            <h1 style="color: #fff; margin: 0; font-size: 28px; font-weight: 700;">Shortcode Bank</h1>
            <p style="color: #94a3b8; margin: 5px 0 0 0; font-size: 15px;">Use these shortcodes to embed MTTS LMS features anywhere on your site.</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
        <?php foreach ( $shortcodes as $tag => $description ) : ?>
            <div style="background: rgba(30, 41, 59, 0.5); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px; transition: all 0.3s ease; position: relative; overflow: hidden;" class="mtts-shortcode-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <code style="background: #1e293b; color: #10b981; padding: 6px 12px; border-radius: 6px; font-family: 'Fira Code', monospace; font-size: 14px; border: 1px solid rgba(16, 185, 129, 0.2);">
                        [<?php echo $tag; ?>]
                    </code>
                    <button onclick="copyToClipboard('[<?php echo $tag; ?>]')" style="background: transparent; border: 0; color: #64748b; cursor: pointer; padding: 5px; border-radius: 4px; transition: color 0.2s;">
                        <span class="dashicons dashicons-admin-page"></span>
                    </button>
                </div>
                <p style="color: #cbd5e1; font-size: 14px; line-height: 1.6; margin: 0;">
                    <?php echo $description; ?>
                </p>
                <div style="position: absolute; bottom: 0; right: 0; width: 60px; height: 60px; background: linear-gradient(135deg, transparent 50%, rgba(124, 58, 237, 0.05) 50%);"></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Shortcode copied to clipboard!');
    });
}
</script>

<style>
.mtts-shortcode-card:hover {
    transform: translateY(-5px);
    border-color: rgba(124, 58, 237, 0.4) !important;
    background: rgba(30, 41, 59, 0.8) !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
}
.mtts-shortcode-card button:hover {
    color: #fff !important;
}
</style>
