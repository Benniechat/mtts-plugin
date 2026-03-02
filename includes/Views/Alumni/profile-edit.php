<?php
/**
 * Alumni Professional Profile Edit (LinkedIn-style)
 */
?>
<div class="mtts-dashboard-section">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
            <h2>💼 Professional Ministry Profile</h2>
            <a href="?view=overview" class="mtts-btn">← Back to Dashboard</a>
        </div>

        <div class="mtts-card" style="padding:0; overflow:hidden;">
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="mtts_alumni_action" value="update_profile">
                <?php wp_nonce_field( 'mtts_alumni_social' ); ?>

                <!-- Media Section -->
                <div class="mtts-profile-banner-container" style="height:150px;">
                    <img src="<?php echo $profile->banner_url ?? ''; ?>" class="mtts-profile-banner-img" id="banner-preview" style="background:#7c3aed;">
                    <label class="mtts-media-upload-btn" style="position:absolute; bottom:10px; right:10px; background:rgba(255,255,255,0.8); backdrop-filter:blur(5px);">
                        <span class="dashicons dashicons-camera"></span> Change Cover
                        <input type="file" name="banner_pic" class="mtts-file-input" accept="image/*" onchange="previewImage(this, 'banner-preview')">
                    </label>
                </div>

                <div style="padding:0 30px 30px;">
                    <div style="position:relative; margin-top:-40px; margin-bottom:20px; display:inline-block;">
                        <img src="<?php echo $profile->profile_picture_url ?? get_avatar_url( get_current_user_id() ); ?>" id="avatar-preview" style="width:100px; height:100px; border-radius:50%; border:4px solid #fff; box-shadow:0 10px 25px rgba(0,0,0,0.1); object-fit:cover;">
                        <label class="mtts-media-upload-btn" style="position:absolute; bottom:0; right:0; padding:5px; width:30px; height:30px; border-radius:50%; justify-content:center; background:#7c3aed; color:white; border:none;">
                            <span class="dashicons dashicons-edit"></span>
                            <input type="file" name="profile_pic" class="mtts-file-input" accept="image/*" onchange="previewImage(this, 'avatar-preview')">
                        </label>
                    </div>

                    <div class="mtts-form-group">
                        <label>Professional Headline</label>
                        <input type="text" name="headline" class="mtts-form-control" value="<?php echo esc_attr( $profile->headline ?? '' ); ?>" placeholder="e.g. Senior Pastor at Grace Assembly | Theological Researcher">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                        <div class="mtts-form-group">
                            <label>Current Ministry/Organization</label>
                            <input type="text" name="current_ministry" class="mtts-form-control" value="<?php echo esc_attr( $profile->current_ministry ?? '' ); ?>" placeholder="Where are you serving?">
                        </div>
                        <div class="mtts-form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="mtts-form-control" value="<?php echo esc_attr( $profile->location ?? '' ); ?>" placeholder="e.g. Lagos, Nigeria">
                        </div>
                    </div>

                    <div class="mtts-form-group">
                        <label>Fellowship Interests (Tags)</label>
                        <input type="text" name="interests" class="mtts-form-control" value="<?php echo esc_attr( $profile->interests ?? '' ); ?>" placeholder="e.g. Missions, Youth Ministry, Biblical Counseling">
                    </div>

                    <div class="mtts-form-group">
                        <label>Ministry Gifts & Graces</label>
                        <input type="text" name="gifts_graces" class="mtts-form-control" value="<?php echo esc_attr( $profile->gifts_graces ?? '' ); ?>" placeholder="e.g. Exegesis, Counseling, Homiletics">
                    </div>

                    <div class="mtts-form-group">
                        <label>Profile Bio / Ministry Summary</label>
                        <textarea name="bio" class="mtts-form-control" rows="5" placeholder="Tell us about your calling and ministry journey..."><?php echo esc_textarea( $profile->bio ?? '' ); ?></textarea>
                    </div>

                    <div style="margin-top:30px; border-top:1px solid #eee; padding-top:20px; text-align:right;">
                        <button type="submit" class="mtts-btn mtts-btn-primary">Update Social Profile</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input, targetId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(targetId).src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
