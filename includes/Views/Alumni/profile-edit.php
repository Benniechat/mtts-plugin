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

        <div class="mtts-card" style="padding:30px;">
            <form method="post" action="">
                <input type="hidden" name="mtts_alumni_action" value="update_profile">
                <?php wp_nonce_field( 'mtts_alumni_social' ); ?>

                <div class="mtts-form-group">
                    <label>Professional Headline</label>
                    <input type="text" name="headline" class="mtts-form-control" value="<?php echo esc_attr( $profile->headline ); ?>" placeholder="e.g. Senior Pastor at Grace Assembly | Theological Researcher">
                </div>

                <div class="mtts-form-group">
                    <label>Current Ministry/Organization</label>
                    <input type="text" name="current_ministry" class="mtts-form-control" value="<?php echo esc_attr( $profile->current_ministry ); ?>" placeholder="Where are you currently serving?">
                </div>

                <div class="mtts-form-group">
                    <label>Ministry Gifts & Graces</label>
                    <input type="text" name="gifts_graces" class="mtts-form-control" value="<?php echo esc_attr( $profile->gifts_graces ); ?>" placeholder="e.g. Exegesis, Counseling, Homiletics, Intercession">
                    <small style="color:#666;">Spiritual and theological competencies.</small>
                </div>

                <div class="mtts-form-group">
                    <label>Ministry Milestones (Timeline)</label>
                    <textarea name="ministry_milestones" class="mtts-form-control" rows="4" placeholder="Timeline of ordination, parish appointments, and academic achievements..."><?php echo esc_textarea( $profile->ministry_milestones ); ?></textarea>
                    <small style="color:#666;">Your significant steps in the ministry journey.</small>
                </div>

                <div class="mtts-form-group">
                    <label>Profile Bio / Ministry Summary</label>
                    <textarea name="bio" class="mtts-form-control" rows="5" placeholder="Tell us about your calling and ministry journey..."><?php echo esc_textarea( $profile->bio ); ?></textarea>
                </div>

                <div class="mtts-form-group">
                    <label>Supplementary Experience (JSON/Text)</label>
                    <textarea name="experience" class="mtts-form-control" rows="3" placeholder="Additional roles and achievements..."><?php echo esc_textarea( $profile->experience ); ?></textarea>
                </div>

                <div style="margin-top:30px; border-top:1px solid #eee; padding-top:20px; text-align:right;">
                    <button type="submit" class="mtts-btn mtts-btn-primary">Save Professional Profile</button>
                </div>
            </form>
        </div>

        <!-- Public Preview Link -->
        <div style="margin-top:30px; text-align:center;">
             <p>Your public portfolio is available at:</p>
             <code><?php echo site_url( '/portfolio/' . \MttsLms\Models\Student::get_by_user(get_current_user_id())->matric_number ); ?></code>
        </div>
    </div>
</div>
