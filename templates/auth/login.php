<div class="mtts-login-page">
    <div class="mtts-login-card">
        <div class="mtts-login-header">
            <div class="mtts-school-logo-placeholder">
                <span class="dashicons dashicons-welcome-learn-more"></span>
                <p>MTTS LOGO</p>
            </div>
            <h2 class="spiritual-gradient-text">Ministerial Entrance</h2>
            <p class="mtts-login-subtitle">"Welcome back to your covenant portal"</p>
        </div>

        <form method="post" action="" class="mtts-form">
            <?php wp_nonce_field( 'mtts_login_action' ); ?>
            
            <?php if ( isset( $_GET['login_error'] ) ) : ?>
                <div class="mtts-alert mtts-alert-danger" style="margin-bottom: 20px;">
                    <span class="dashicons dashicons-warning"></span> Invalid credentials. Discern and try again.
                </div>
            <?php endif; ?>

            <div class="mtts-form-group">
                <label for="mtts_username">Username or Email</label>
                <div class="mtts-input-with-icon">
                    <span class="dashicons dashicons-admin-users"></span>
                    <input type="text" name="mtts_username" id="mtts_username" required class="mtts-form-control" placeholder="Ministerial Identity">
                </div>
            </div>

            <div class="mtts-form-group">
                <label for="mtts_password">Password</label>
                <div class="mtts-input-with-icon">
                    <span class="dashicons dashicons-lock"></span>
                    <input type="password" name="mtts_password" id="mtts_password" required class="mtts-form-control" placeholder="••••••••">
                </div>
            </div>

            <div class="mtts-form-actions" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
                <label class="mtts-checkbox-container">
                    <input type="checkbox" name="mtts_remember">
                    <span class="checkmark"></span>
                    Keep me in Koinonia
                </label>
                <a href="<?php echo wp_lostpassword_url(); ?>" class="mtts-lost-password">Lost Key?</a>
            </div>

            <button type="submit" name="mtts_login_submit" class="mtts-btn mtts-btn-primary mtts-btn-block">
                Enter Portal <span class="dashicons dashicons-arrow-right-alt"></span>
            </button>
        </form>
    </div>
</div>
