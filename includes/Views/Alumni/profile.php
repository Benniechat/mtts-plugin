<?php
/**
 * Alumni Professional Profile View (LinkedIn-style)
 */
$user_id = isset($_GET['uid']) ? intval($_GET['uid']) : get_current_user_id();
$viewing_user = get_userdata($user_id);
$profile = \MttsLms\Models\AlumniProfile::get_by_user( $user_id );
$student = \MttsLms\Models\Student::get_by_user( $user_id );
?>

<div class="mtts-dashboard-section" style="max-width: 1000px; margin: 0 auto;">
    <div class="koinonia-glass" style="overflow:hidden; margin-bottom:25px;">
        <!-- Banner -->
        <div class="mtts-profile-banner-container">
            <img src="<?php echo $profile->banner_url ?: ''; ?>" class="mtts-profile-banner-img" style="background: linear-gradient(135deg, #7c3aed, #fbbf24);">
            <img src="<?php echo $profile->profile_picture_url ?: get_avatar_url($user_id); ?>" class="mtts-profile-overlap-avatar">
        </div>

        <!-- Header Info -->
        <div style="padding: 60px 30px 30px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <h1 style="margin:0; font-size:28px;"><?php echo esc_html($viewing_user->display_name); ?></h1>
                    <p style="font-size:18px; color:#1e293b; margin:5px 0;"><?php echo esc_html($profile->headline ?? 'Theologian & Minister'); ?></p>
                    <p style="font-size:14px; color:#64748b; margin:5px 0;">
                        <?php echo esc_html($profile->location ?? 'Global Missions'); ?> • 
                        <span style="color:#7c3aed; font-weight:700;">500+ Connections</span>
                    </p>
                </div>
                <?php if ($user_id === get_current_user_id()) : ?>
                    <a href="?view=profile-edit" class="mtts-btn mtts-btn-primary" style="border-radius:20px;">Edit Profile</a>
                <?php else : ?>
                    <button class="mtts-btn mtts-btn-primary" style="border-radius:20px;">Connect</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:25px;">
        <div class="nexus-main-col">
            <!-- About Section -->
            <div class="koinonia-glass mtts-social-card">
                <h4 style="margin-top:0;">About</h4>
                <div style="font-size:15px; line-height:1.6; color:#334155;">
                    <?php echo nl2br(esc_html($profile->bio ?? 'No bio provided yet.')); ?>
                </div>
            </div>

            <!-- Experience / Ministry Milestones -->
            <div class="koinonia-glass mtts-social-card">
                <h4 style="margin-top:0;">Ministry Journey</h4>
                <div style="margin-top:20px;">
                    <div style="display:flex; gap:15px; margin-bottom:20px;">
                        <div style="width:48px; height:48px; background:#f1f5f9; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                            <span class="dashicons dashicons-calendar-alt" style="color:#7c3aed;"></span>
                        </div>
                        <div>
                            <h5 style="margin:0; font-size:16px;">Current Ministry</h5>
                            <p style="margin:5px 0; font-size:14px;"><?php echo esc_html($profile->current_ministry ?? 'Active Service'); ?></p>
                        </div>
                    </div>
                    <!-- More milestones could be looped here if structured -->
                </div>
            </div>
        </div>

        <div class="nexus-sidebar-col">
            <!-- Interests -->
            <div class="koinonia-glass mtts-social-card">
                <h4 style="margin-top:0;">Spiritual Interests</h4>
                <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:15px;">
                    <?php 
                    $interests_str = $profile->interests ?? '';
                    $interests = !empty($interests_str) ? explode(',', $interests_str) : [];
                    foreach ($interests as $interest) : if(!trim($interest)) continue; ?>
                        <span class="ministry-tag"><?php echo esc_html(trim($interest)); ?></span>
                    <?php endforeach; ?>
                    <?php if (empty($interests)) echo '<p style="color:#64748b; font-size:13px; margin:0;">No interests listed.</p>'; ?>
                </div>
            </div>

            <!-- Suggested Fellowship -->
            <div class="koinonia-glass mtts-social-card">
                <h4 style="margin-top:0;">Suggested Fellows</h4>
                <!-- Mini list of other alumni -->
            </div>
        </div>
    </div>
</div>
