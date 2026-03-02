<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="mtts-card mtts-password-change-card" style="max-width: 500px; margin: 50px auto; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: #fff; border-top: 5px solid #7c3aed;">
    <div style="text-align: center; margin-bottom: 30px;">
        <span class="dashicons dashicons-lock" style="font-size: 50px; width: 50px; height: 50px; color: #7c3aed;"></span>
        <h2 style="margin-top: 15px;">Security Update Required</h2>
        <p class="text-muted">For your security, you must change your default password before accessing the student portal.</p>
    </div>

    <form method="post" action="">
        <?php wp_nonce_field( 'mtts_change_password' ); ?>
        
        <div class="mtts-form-group">
            <label for="pass1">New Password</label>
            <input type="password" name="pass1" id="pass1" class="mtts-form-control" required minlength="8" placeholder="At least 8 characters">
        </div>

        <div class="mtts-form-group">
            <label for="pass2">Confirm New Password</label>
            <input type="password" name="pass2" id="pass2" class="mtts-form-control" required minlength="8" placeholder="Repeat new password">
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" name="mtts_change_password" class="mtts-btn mtts-btn-primary" style="width: 100%;">Update Password & Access Dashboard</button>
        </div>
    </form>

    <div style="margin-top: 20px; text-align: center; font-size: 13px; color: #64748b;">
        <p><span class="dashicons dashicons-shield" style="font-size: 16px; width: 16px; height: 16px;"></span> Your connection is encrypted and secure.</p>
    </div>
</div>

<style>
.mtts-password-change-card h2 { color: #1e293b; font-weight: 800; }
.mtts-password-change-card .text-muted { color: #64748b; font-size: 14px; line-height: 1.5; }
</style>
