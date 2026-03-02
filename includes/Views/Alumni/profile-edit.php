<?php
/**
 * Profile Edit – Stitch "Refine Identity" UI
 */
?>

<style>
.st-edit-layout {
    max-width: 820px;
    margin: 0 auto;
}
.st-edit-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(13,39,165,.08);
    overflow: hidden;
    margin-bottom: 20px;
}
.st-edit-card-header {
    padding: 18px 28px;
    border-bottom: 1px solid #f3f4f8;
    display: flex;
    align-items: center;
    gap: 10px;
}
.st-edit-card-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #1a1a2e;
}
.st-edit-card-body { padding: 24px 28px; }
.st-form-group { margin-bottom: 20px; }
.st-form-group label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 8px;
}
.st-form-control {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #1a1a2e;
    font-family: inherit;
    outline: none;
    background: #fff;
    box-sizing: border-box;
    transition: border-color .15s;
}
.st-form-control:focus { border-color: #6b21a8; }
.st-form-control textarea { resize: vertical; min-height: 100px; }
.st-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.st-upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: rgba(255,255,255,.85);
    border: 1.5px solid rgba(255,255,255,.6);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    color: #1a1a2e;
    backdrop-filter: blur(4px);
    transition: background .15s;
}
.st-upload-btn:hover { background: rgba(255,255,255,.98); }
@media(max-width:600px) { .st-grid-2 { grid-template-columns: 1fr; } }
</style>

<div class="st-edit-layout">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="margin:0;font-size:24px;font-weight:800;color:#1a1a2e;">Refine Your Identity</h2>
            <p style="color:#6b7280;margin:5px 0 0;font-size:14px;">Manage your ministerial profile and personal details.</p>
        </div>
        <a href="?view=profile" class="stitch-btn-outline" style="border-radius:8px;">← View Public Profile</a>
    </div>

    <form method="post" action="" enctype="multipart/form-data" id="st-profile-form">
        <?php wp_nonce_field('mtts_alumni_social'); ?>
        <input type="hidden" name="mtts_alumni_action" value="update_profile">

        <!-- Media Section -->
        <div class="st-edit-card">
            <div style="height:160px;background:linear-gradient(135deg,#6b21a8,#7c3aed);position:relative;overflow:hidden;">
                <img id="st-banner-preview" src="<?php echo esc_url($profile->banner_url ?: ''); ?>"
                     style="width:100%;height:100%;object-fit:cover;<?php echo $profile->banner_url ? '' : 'display:none;'; ?>">
                <label class="st-upload-btn" style="position:absolute;bottom:12px;right:16px;">
                    📷 Change Cover
                    <input type="file" name="banner_pic" accept="image/*" style="display:none;" onchange="stPreviewImg(this,'st-banner-preview')">
                </label>
            </div>
            <div style="padding:0 28px 24px;">
                <div style="position:relative;margin-top:-40px;display:inline-block;">
                    <img id="st-avatar-preview"
                         src="<?php echo esc_url($profile->profile_picture_url ?: get_avatar_url(get_current_user_id())); ?>"
                         style="width:96px;height:96px;border-radius:50%;border:4px solid #fff;object-fit:cover;box-shadow:0 4px 16px rgba(0,0,0,.12);">
                    <label style="position:absolute;bottom:2px;right:2px;width:30px;height:30px;border-radius:50%;background:#6b21a8;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid #fff;">
                        <span style="color:#fff;font-size:14px;">✏</span>
                        <input type="file" name="profile_pic" accept="image/*" style="display:none;" onchange="stPreviewImg(this,'st-avatar-preview')">
                    </label>
                </div>
            </div>
        </div>

        <!-- Basic Info -->
        <div class="st-edit-card">
            <div class="st-edit-card-header">
                <span style="font-size:18px;">👤</span>
                <h4>Basic Information</h4>
            </div>
            <div class="st-edit-card-body">
                <div class="st-form-group">
                    <label>Professional Headline</label>
                    <input type="text" name="headline" class="st-form-control"
                           value="<?php echo esc_attr($profile->headline ?? ''); ?>"
                           placeholder="e.g. Senior Pastor at Grace Assembly | Theological Researcher">
                </div>
                <div class="st-grid-2">
                    <div class="st-form-group" style="margin-bottom:0;">
                        <label>Current Ministry / Organization</label>
                        <input type="text" name="current_ministry" class="st-form-control"
                               value="<?php echo esc_attr($profile->current_ministry ?? ''); ?>"
                               placeholder="Where are you currently serving?">
                    </div>
                    <div class="st-form-group" style="margin-bottom:0;">
                        <label>Location</label>
                        <input type="text" name="location" class="st-form-control"
                               value="<?php echo esc_attr($profile->location ?? ''); ?>"
                               placeholder="e.g. Lagos, Nigeria">
                    </div>
                </div>
            </div>
        </div>

        <!-- Ministry Profile -->
        <div class="st-edit-card">
            <div class="st-edit-card-header">
                <span style="font-size:18px;">✝</span>
                <h4>Ministry Profile</h4>
            </div>
            <div class="st-edit-card-body">
                <div class="st-form-group">
                    <label>Profile Bio / Ministry Summary</label>
                    <textarea name="bio" class="st-form-control" rows="5" placeholder="Tell us about your calling and ministry journey..."><?php echo esc_textarea($profile->bio ?? ''); ?></textarea>
                </div>
                <div class="st-grid-2">
                    <div class="st-form-group" style="margin-bottom:0;">
                        <label>Spiritual Interests <span style="font-weight:400;text-transform:none;">(comma-separated)</span></label>
                        <input type="text" name="interests" class="st-form-control"
                               value="<?php echo esc_attr($profile->interests ?? ''); ?>"
                               placeholder="e.g. Missions, Youth Ministry, Counseling">
                    </div>
                    <div class="st-form-group" style="margin-bottom:0;">
                        <label>Gifts & Graces</label>
                        <input type="text" name="gifts_graces" class="st-form-control"
                               value="<?php echo esc_attr($profile->gifts_graces ?? ''); ?>"
                               placeholder="e.g. Exegesis, Homiletics, Counseling">
                    </div>
                </div>
            </div>
        </div>

        <!-- Experience & Milestones -->
        <div class="st-edit-card">
            <div class="st-edit-card-header">
                <span style="font-size:18px;">📜</span>
                <h4>Experience & Milestones</h4>
            </div>
            <div class="st-edit-card-body">
                <div class="st-form-group">
                    <label>Experience / Ministry Background</label>
                    <textarea name="experience" class="st-form-control" rows="4" placeholder="Describe your previous ministry roles and experiences..."><?php echo esc_textarea($profile->experience ?? ''); ?></textarea>
                </div>
                <div class="st-form-group" style="margin-bottom:0;">
                    <label>Ministry Milestones & Publications <span style="font-weight:400;text-transform:none;">(one per line)</span></label>
                    <textarea name="ministry_milestones" class="st-form-control" rows="4" placeholder="e.g. Published: The Digital Pulpit, 2023&#10;Ordained as Senior Pastor, 2018"><?php echo esc_textarea($profile->ministry_milestones ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Skills -->
        <div class="st-edit-card">
            <div class="st-edit-card-header">
                <span style="font-size:18px;">🛠</span>
                <h4>Skills & Competencies</h4>
            </div>
            <div class="st-edit-card-body">
                <div class="st-form-group" style="margin-bottom:0;">
                    <label>Skills <span style="font-weight:400;text-transform:none;">(comma-separated)</span></label>
                    <input type="text" name="skills" class="st-form-control"
                           value="<?php echo esc_attr($profile->skills ?? ''); ?>"
                           placeholder="e.g. Biblical Greek, Preaching, Community Development, Leadership">
                </div>
            </div>
        </div>

        <!-- Save -->
        <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 0;">
            <a href="?view=profile" class="stitch-btn-outline" style="border-radius:8px;">Cancel</a>
            <button type="submit" class="stitch-btn-primary" style="padding:11px 32px;font-size:15px;border-radius:8px;">💾 Save Profile</button>
        </div>
    </form>

    <!-- Success message after redirect -->
    <?php if (isset($_GET['profile_updated'])): ?>
    <div style="position:fixed;bottom:24px;right:24px;background:#6b21a8;color:#fff;border-radius:10px;padding:14px 22px;font-weight:600;box-shadow:0 6px 20px rgba(13,39,165,.3);animation:fadeInUp .4s ease;z-index:9999;">
        ✓ Profile updated successfully!
    </div>
    <script>setTimeout(()=>document.querySelector('[style*="fadeInUp"]')?.remove(), 4000);</script>
    <?php endif; ?>
</div>

<style>
@keyframes fadeInUp { from { opacity:0;transform:translateY(20px); } to { opacity:1;transform:translateY(0); } }
</style>

<script>
function stPreviewImg(input, targetId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const el = document.getElementById(targetId);
            el.src = e.target.result;
            el.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
